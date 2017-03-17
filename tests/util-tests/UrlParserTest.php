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
    }

    /**
     * @expectedException \Generics\Socket\InvalidUrlException
     * @expectedExceptionMessage This URL does not contain a host part
     */
    public function testInvalidHost()
    {
        UrlParser::parseUrl("http:///abc");
    }

    /**
     * @expectedException \Generics\Socket\InvalidUrlException
     * @expectedExceptionMessage This URL does not contain a scheme part
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
