<?php

namespace Generics\Tests;

use Generics\Socket\Url;

class UrlTest extends \PHPUnit_Framework_TestCase
{
    public function testUrlSimple()
    {
        $u = "http://www.nkey.de/";
        $url = new Url($u);
        $this->assertEquals($u, strval($url));
    }
}