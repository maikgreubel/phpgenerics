<?php

namespace Generics\Tests;

use Generics\Util\UrlParser;

class UrlParserTest extends \PHPUnit_Framework_TestCase
{
    public function testUrlParser()
    {
        $urlToParse = "http://www.nkey.de/index.php";

        $url = UrlParser::parseUrl($urlToParse);

        $this->assertEquals(80, $url->getPort());
        $this->assertEquals('/index.php', $url->getPath());
        $this->assertEquals('http', $url->getScheme());
        $this->assertEquals('www.nkey.de', $url->getAddress());
    }
}
