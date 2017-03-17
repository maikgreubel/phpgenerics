<?php

namespace Generics\Tests;

use Generics\Socket\Url;

class UrlTest extends \PHPUnit\Framework\TestCase
{
    public function testUrlSimple()
    {
        $u = "http://www.nkey.de/";
        $url = new Url($u);
        $this->assertEquals($u, strval($url));
    }

    public function testUrlHttps()
    {
        $u = "https://www.nkey.de/";
        $url = new Url($u);
        $this->assertEquals($u, strval($url));
    }

    public function testUrlFtp()
    {
        $u = "ftp://www.nkey.de/";
        $url = new Url($u);
        $this->assertEquals($u, strval($url));
    }

    public function testUrlWithPort()
    {
        $u = "http://www.nkey.de:8080/";
        $url = new Url($u);
        $this->assertEquals($u, strval($url));
    }
}
