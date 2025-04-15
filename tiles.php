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
    public $nextx;
    public $nexty;
    public $spritex=0;
    public $spritey=15;
    public $collision=1;
    public function __construct($x,$y,$mapArray) {
        $this->x = $x;
        $this->y = $y;
        $this->triangulate($mapArray);
    }
    public function triangulate($mapArray){
        if($mapArray[$this->x-1][$this->y]->collision==0){
            $this->nextx = $this->x-1;
            $this->nexty = $this->y;
        }
        else if ($mapArray[$this->x][$this->y-1]->collision==0){
            $this->nextx = $this->x;
            $this->nexty = $this->y-1;
        }
        else if ($mapArray[$this->x+1][$this->y]->collision==0){
            $this->nextx = $this->x+1;
            $this->nexty = $this->y;
        }
        else if($mapArray[$this->x][$this->y+1]->collision==0){
            $this->nextx = $this->x;
            $this->nexty = $this->y+1;
        }
    }
    public function move($mapArray){
        $this->x = $this->nextx;
        $this->y = $this->nexty;
        $this->triangulate($mapArray);
    }
    
    public function pingas(){
        echo "pingas";
    }
}