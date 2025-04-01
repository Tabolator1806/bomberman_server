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
    public $spritex=0;
    public $spritey=15;
}