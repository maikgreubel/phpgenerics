<?php

namespace Generics\Tests;

use Generics\Util\UrlParser;

class UrlParserTest extends \PHPUnit\Framework\TestCase
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

    public function testUrlParserSimpleUrl()
    {
        $urlToParse = "http://www.nkey.de";

        $url = UrlParser::parseUrl($urlToParse);

        $this->assertEquals(80, $url->getPort());
        $this->assertEquals('/', $url->getPath());
        $this->assertEquals('http', $url->getScheme());
        $this->assertEquals('www.nkey.de', $url->getAddress());
        $this->assertEmpty($url->getQueryString());
        $this->assertEquals($urlToParse . "/", $url->getUrlString());
    }

    public function testQueryString()
    {
        $urlToParse = "http://www.nkey.de/?param=1&func=print";
        $url = UrlParser::parseUrl($urlToParse);

        $this->assertEquals(80, $url->getPort());
        $this->assertEquals('/', $url->getPath());
        $this->assertEquals('http', $url->getScheme());
        $this->assertEquals('www.nkey.de', $url->getAddress());
        $this->assertEquals('param=1&func=print', $url->getQueryString());
        $this->assertEquals($urlToParse, $url->getUrlString());
    }

    /**
     * @expectedException \Generics\Socket\InvalidUrlException
     * @expectedExceptionMessage does not contain necessary parts
     */
    public function testInvalidHost()
    {
        UrlParser::parseUrl("http:///abc");
    }

    /**
     * @expectedException \Generics\Socket\InvalidUrlException
     * @expectedExceptionMessage does not contain necessary parts
     */
    public function testInvalidScheme()
    {
        UrlParser::parseUrl("//www.nkey.de/");
    }

    /**
     * @expectedException \Generics\Socket\InvalidUrlException
     * @expectedExceptionMessage Scheme file is not handled!
     */
    public function testUnhandledScheme()
    {
        UrlParser::parseUrl("file://www.nkey.de/");
    }

    public function testUrlWithPort()
    {
        $url = UrlParser::parseUrl("http://www.nkey.de:80/index.php");
        $this->assertEquals(80, $url->getPort());
    }

    public function testFtpUrl()
    {
        $url = UrlParser::parseUrl("ftp://www.nkey.de/");
        $this->assertEquals(21, $url->getPort());
    }
}
