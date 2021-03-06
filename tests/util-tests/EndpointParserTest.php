<?php

namespace Generics\Tests;

use Generics\Util\EndpointParser;

class EndpointParserTest extends \PHPUnit\Framework\TestCase
{
    public function testEndpointParserHttp()
    {
        $url = "http://www.nkey.de/index.php";

        $endpoint = EndpointParser::parseUrl($url);

        $this->assertEquals('www.nkey.de', $endpoint->getAddress());
        $this->assertEquals(80, $endpoint->getPort());
    }

    /**
     * @expectedException \Generics\Socket\InvalidUrlException
     */
    public function testInvalidUrlException()
    {
        $url = "htt://www.nkey.de/index.php";

        EndpointParser::parseUrl($url);
    }
}
