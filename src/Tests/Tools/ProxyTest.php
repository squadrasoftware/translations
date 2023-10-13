<?php

namespace App\Tests\Tools;

use App\Tests\Tools\Fixtures\Something;
use App\Tools\Proxy;
use PHPUnit\Framework\TestCase;

class ProxyTest extends TestCase
{
    private $stuff;

    public function setUp(): void
    {
        parent::setUp();

        $this->stuff = new Proxy(new Something());
    }

    public function testAccessPublicProperty()
    {
        $this->stuff->a = 10;
        $this->assertEquals(10, $this->stuff->a);
    }

    public function testAccessProtectedProperty()
    {
        $this->stuff->b = 19;
        $this->assertEquals(19, $this->stuff->b);
    }

    public function testAccessPrivateProperty()
    {
        $this->stuff->c = 24;
        $this->assertEquals(24, $this->stuff->c);
    }

    public function testAccessPublicStaticProperty()
    {
        $this->stuff->d = 31;
        $this->assertEquals(31, $this->stuff->d);
    }

    public function testAccessProtectedStaticProperty()
    {
        $this->stuff->e = 42;
        $this->assertEquals(42, $this->stuff->e);
    }

    public function testAccessPrivateStaticProperty()
    {
        $this->stuff->f = 5;
        $this->assertEquals(5, $this->stuff->f);
    }

    public function testAccessPublicMethod()
    {
        $this->assertEquals("g...\n", $this->stuff->g());
    }

    public function testAccessProtectedMethod()
    {
        $this->assertEquals("h...\n", $this->stuff->h());
    }

    public function testAccessPrivateMethod()
    {
        $this->assertEquals("i...\n", $this->stuff->i());
    }

    public function testAccessPublicStaticMethod()
    {
        $this->assertEquals("j...\n", $this->stuff->j());
    }

    public function testAccessProtectedStaticMethod()
    {
        $this->assertEquals("k...\n", $this->stuff->k());
    }

    public function testAccessPrivateStaticMethod()
    {
        $this->assertEquals("l...\n", $this->stuff->l());
    }
}