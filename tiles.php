<?php

abstract class Tile{
    public $x;
    public $y;
    public $spritex;
    public $spritey;
    public $collision;
    public function __construct($x,$y) {
        $this->x = $x;
        $this->y = $y;
    }
}

abstract class Entity extends Tile{
    public $hp = 1;
}

class Border extends Tile{
    public $spritex=3;
    public $spritey=3;
    public $collision=1;
}

class BrickWall extends Tile{
    public $spritex=4;
    public $spritey=3;
    public $collision=1;
}

class Grass extends Tile{
    public $spritex=3;
    public $spritey=8;
    public $collision=0;
}

class Ballon extends Entity{
    public $direction = "";
    // public $spritex=0;
    // public $spritey=15;
    public $collision=1;
    public $nextx = 0;
    public $nexty = 0;
    public function __construct($x,$y,$mapArray) {
        $this->x = $x;
        $this->y = $y;
        $this->nextx = $x;
        $this->nexty = $y;
        switch(random_int(0,3)){
            case 0:
                $this->direction = "left";
                break;
            case 1:
                $this->direction = "right";
                break;
            case 2:
                $this->direction = "up";
                break;
            case 3:
                $this->direction = "down";
                break;    
            
        }
        $this->triangulate($mapArray);
    }
    public function triangulate($mapArray){
        if($this->direction=="left"){
            if($mapArray[$this->x-1][$this->y]->collision==1){
                switch(random_int(0,2)){
                    case 0:
                        if ($mapArray[$this->x][$this->y-1]->collision==0){
                            $this->direction="up";
                            $this->nextx = $this->x;
                            $this->nexty = $this->y-1;
                        }
                        else{
                            $this->triangulate($mapArray);
                        }
                        break;
                    case 1:
                        if ($mapArray[$this->x+1][$this->y]->collision==0){
                            $this->direction="right";
                            $this->nextx = $this->x+1;
                            $this->nexty = $this->y;
                        }
                        else{
                            $this->triangulate($mapArray);
                        }
                        break;
                    case 2:
                        if($mapArray[$this->x][$this->y+1]->collision==0){
                            $this->direction="down";
                            $this->nextx = $this->x;
                            $this->nexty = $this->y+1;
                        }
                        else{
                            $this->triangulate($mapArray);
                        }
                        break;
                }
            }
            else{
                $this->nextx = $this->x-1;
                $this->nexty = $this->y;
            }
        }
        else if($this->direction=="right"){
            if($mapArray[$this->x+1][$this->y]->collision==1){
                switch(random_int(0,2)){
                    case 0:
                        if ($mapArray[$this->x][$this->y-1]->collision==0){
                            $this->direction="up";
                            $this->nextx = $this->x;
                            $this->nexty = $this->y-1;
                        }
                        else{
                            $this->triangulate($mapArray);
                        }
                        break;
                    case 1:
                        if ($mapArray[$this->x-1][$this->y]->collision==0){
                            $this->direction="left";
                            $this->nextx = $this->x-1;
                            $this->nexty = $this->y;
                        }
                        else{
                            $this->triangulate($mapArray);
                        }
                        break;
                    case 2:
                        if($mapArray[$this->x][$this->y+1]->collision==0){
                            $this->direction="down";
                            $this->nextx = $this->x;
                            $this->nexty = $this->y+1;
                        }
                        else{
                            $this->triangulate($mapArray);
                        }
                        break;
                }
            }
            else{
                $this->nextx = $this->x+1;
                $this->nexty = $this->y;
            }
        }
        else if($this->direction=="up"){
            if($mapArray[$this->x][$this->y-1]->collision==1){
                switch(random_int(0,2)){
                    case 0:
                        if ($mapArray[$this->x-1][$this->y]->collision==0){
                            $this->direction="left";
                            $this->nextx = $this->x-1;
                            $this->nexty = $this->y;
                        }
                        else{
                            $this->triangulate($mapArray);
                        }
                        break;
                    case 1:
                        if ($mapArray[$this->x+1][$this->y]->collision==0){
                            $this->direction="right";
                            $this->nextx = $this->x+1;
                            $this->nexty = $this->y;
                        }
                        else{
                            $this->triangulate($mapArray);
                        }
                        break;
                    case 2:
                        if($mapArray[$this->x][$this->y+1]->collision==0){
                            $this->direction="down";
                            $this->nextx = $this->x;
                            $this->nexty = $this->y+1;
                        }
                        else{
                            $this->triangulate($mapArray);
                        }
                        break;
                }
            }
            else{
                $this->nextx = $this->x;
                $this->nexty = $this->y-1;
            }
        }
        else if($this->direction=="down"){
            if($mapArray[$this->x][$this->y+1]->collision==1){
                switch(random_int(0,2)){
                    case 0:
                        if ($mapArray[$this->x-1][$this->y]->collision==0){
                            $this->direction="left";
                            $this->nextx = $this->x-1;
                            $this->nexty = $this->y;
                        }
                        else{
                            $this->triangulate($mapArray);
                        }
                        break;
                    case 1:
                        if ($mapArray[$this->x+1][$this->y]->collision==0){
                            $this->direction="right";
                            $this->nextx = $this->x+1;
                            $this->nexty = $this->y;
                        }
                        else{
                            $this->triangulate($mapArray);
                        }
                        break;
                    case 2:
                        if($mapArray[$this->x][$this->y-1]->collision==0){
                            $this->direction="up";
                            $this->nextx = $this->x;
                            $this->nexty = $this->y-1;
                        }
                        else{
                            $this->triangulate($mapArray);
                        }
                        break;
                }
            }
            else{
                $this->nextx = $this->x;
                $this->nexty = $this->y+1;
            }
        }
        // if($mapArray[$this->x-1][$this->y]->collision==0){
        //     $this->nextx = $this->x-1;
        //     $this->nexty = $this->y;
        // }
        // else if ($mapArray[$this->x][$this->y-1]->collision==0){
        //     $this->nextx = $this->x;
        //     $this->nexty = $this->y-1;
        // }
        // else if ($mapArray[$this->x+1][$this->y]->collision==0){
        //     $this->nextx = $this->x+1;
        //     $this->nexty = $this->y;
        // }
        // else if($mapArray[$this->x][$this->y+1]->collision==0){
        //     $this->nextx = $this->x;
        //     $this->nexty = $this->y+1;
        // }
    }
    public function move($mapArray){
        $this->x = $this->nextx;
        $this->y = $this->nexty;
        $this->triangulate($mapArray);
    }
    
    
    // public function pingas(){
    //     echo "pingas";
    // }
}
class Player extends Entity{
    public function __construct(){
        $this->x=1;
        $this->y=1;
    }    
}