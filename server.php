    <?php
    require_once("tiles.php");
    $host = 'localhost'; // moja domena w ct8.pl
    $port = 46089; // zarezerwowany w panelu port
    $transport = 'http';
    $server = stream_socket_server("tcp://localhost:46089", $errno, $errstr);
    if (!$server) {
        die("$errstr ($errno)");
    }
    $clients = array($server); // tablica klientów
    $write  = NULL;
    $except = NULL;
    $mapWidth = 31;
    $mapHeight = 13;
    $mapLayout=generate_map($mapWidth,$mapHeight);
    $entitiesLayout = generate_enemy($mapWidth,$mapHeight,$mapLayout);


    while (true) {
        $changed = $clients;
        stream_select($changed, $write, $except, 1);
    
        if (in_array($server, $changed)) {
            $client = @stream_socket_accept($server);
            if (!$client) {
                continue;
            }
            $clients[] = $client;
            $ip = stream_socket_get_name($client, true);
            echo "New Client connected from $ip\n";

            stream_set_blocking($client, true);
            $headers = fread($client, 1500);
            handshake($client, $headers, $host, $port);
            stream_set_blocking($client, false);

            $data=["msg" => "Nastąpiło połączenie"];
            send_message($clients, mask(json_encode([
                "gamedata"=>[
                    "mapdata"=>[
                        "mapLayout"=>$mapLayout,
                        "mapWidth"=>$mapWidth,
                        "mapHeight"=>$mapHeight
                    ],
                    "entitydata"=>[
                        "entityLayout"=>$entitiesLayout
                    ]
                ]
            ])));

            send_message($clients, mask(json_encode($data))); //połączenie -> aktualne dane

            $found_socket = array_search($server, $changed);
            unset($changed[$found_socket]);
        }

        foreach ($changed as $changed_socket) {   // wiadomość od klienta
            print_r($changed_socket);
        
            $ip = stream_socket_get_name($changed_socket, true);
            $buffer = stream_get_contents($changed_socket);
        
            if ($buffer == false) {
                echo "Client Disconnected from $ip\n";
                @fclose($changed_socket);
                $found_socket = array_search($changed_socket, $clients);
                unset($clients[$found_socket]);
            }
        
            $unmasked = unmask($buffer);
            if ($unmasked != "") {
                echo "\nReceived a Message from $ip:\n\"$unmasked\" \n";
            }
        
            $response = mask($unmasked);
            if (!is_closing_frame($buffer)) {
                $response = mask($unmasked);
                send_message($clients, $response);
            }
        }
        foreach($entitiesLayout as $enemy){
            $enemy->move($mapLayout);
            print_r($enemy->x . ", ". $enemy->y. "\n");
        }
        print_r("---------------------------------------------");
        send_message($clients, mask(json_encode([
            "gamedata"=>[
                "mapdata"=>[
                    "mapLayout"=>$mapLayout,
                    "mapWidth"=>$mapWidth,
                    "mapHeight"=>$mapHeight
                ],
                "entitydata"=>[
                    "entityLayout"=>$entitiesLayout
                ]
            ]
        ])));
    }
    fclose($server);
    function is_closing_frame($text) {
        $opcode = @ord($text[0]) & 0x0f;
        return $opcode == 8;
    }

    function unmask($text)
    {
        $length = @ord($text[1]) & 127;
        if ($length == 126) {
            $masks = substr($text, 4, 4);
            $data = substr($text, 8);
        } elseif ($length == 127) {
            $masks = substr($text, 10, 4);
            $data = substr($text, 14);
        } else {
            $masks = substr($text, 2, 4);
            $data = substr($text, 6);
        }
        $text = "";
        for ($i = 0; $i < strlen($data); ++$i) {
            $text .= $data[$i] ^ $masks[$i % 4];
        }
        return $text;
    }

    function mask($text)
    {
        $b1 = 0x80 | (0x1 & 0x0f);
        $length = strlen($text);
        if ($length <= 125)
            $header = pack('CC', $b1, $length);
        elseif ($length > 125&& $length < 65536)
            $header = pack('CCn', $b1, 126, $length);
        elseif ($length >= 65536)
            $header = pack('CCNN', $b1, 127, $length);
        return $header . $text;
    }

    function handshake($client, $rcvd, $host, $port)
    {
        $headers = array();
        $lines = preg_split("/\r\n/", $rcvd);
        foreach ($lines as $line) {
            $line = rtrim($line);
            if (preg_match('/\A(\S+): (.*)\z/', $line, $matches)) {
                $headers[$matches[1]] = $matches[2];
            }
        }
        $secKey = $headers['Sec-WebSocket-Key'];
        $secAccept = base64_encode(pack('H*', sha1($secKey . '258EAFA5-E914-47DA-95CA-C5AB0DC85B11')));
        //hand shaking header
        $upgrade  = "HTTP/1.1 101 Web Socket Protocol Handshake\r\n" .
            "Upgrade: websocket\r\n" .
            "Connection: Upgrade\r\n" .
            "WebSocket-Origin: $host\r\n" .
            "WebSocket-Location: ssl://$host:$port\r\n" .
            "Sec-WebSocket-Version: 13\r\n".
            "Sec-WebSocket-Accept:$secAccept\r\n\r\n";
        fwrite($client, $upgrade);
    }

    function send_message($clients, $msg)
    {
        foreach ($clients as $changed_socket) {
            @fwrite($changed_socket, $msg);
        }
    }
    function generate_map($w,$h){
        $array = [];
        for($x=0;$x<$w;$x++){
            array_push($array,[]);
            for($y=0;$y<$h;$y++){
                if($x==0||$x==$w-1||$y==0||$y==$h-1||($x%2==0&&$y%2==0)){
                    array_push($array[$x],new Border($x,$y));
                }
                else if(($x==1||$x==2)&&($y==1||$y==2)){
                    array_push($array[$x],new Grass($x,$y));
                }
                else{
                    $randomInt = random_int(0,5);
                    switch ($randomInt) {
                        case 0:
                            array_push($array[$x],new BrickWall($x,$y));
                            break;
                        // case 3:
                        //     array_push($array,new Ballon($x,$y));
                        //     break;
                        case $randomInt>0:
                            array_push($array[$x],new Grass($x,$y));
                            break;
                        default:
                            echo "wtf";
                    }
                }
            }
        }
        return $array;
    }
    function generate_enemy($w,$h,$mapArray){
        $array = [];
        $ballonAmount = 1;
        for($x=0;$x<$w;$x++){
            for($y=0;$y<$h;$y++){
                if($mapArray[$x][$y]->collision==0){
                    $randomInt = random_int(0,9);
                    if($randomInt==0&&$ballonAmount>0){
                        array_push($array,new Ballon($x,$y,$mapArray));
                        $ballonAmount-=1;
                    }
                }
            }
        }
        return $array;
    }
?>