<?php

namespace App\Tests\Tools\Fixtures;

class Something
{
    public static $d = "d...\n";
    protected static $e = "e...\n";
    private static $f = "f...\n";
    public $a = "a...\n";
    protected $b = "b...\n";
    private $c = "c...\n";

    public static function j()
    {
        return "j...\n";
    }

    protected static function k()
    {
        return "k...\n";
    }

    private static function l()
    {
        return "l...\n";
    }

    public function g()
    {
        return "g...\n";
    }

    protected function h()
    {
        return "h...\n";
    }

    private function i()
    {
        return "i...\n";
    }
}
