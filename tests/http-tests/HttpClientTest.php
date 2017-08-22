<?php

namespace Generics\Tests;

use Generics\Client\HttpClient;
use Generics\Socket\Url;
use Generics\Util\UrlParser;
use Generics\Streams\MemoryStream;

class HttpClientTest extends \PHPUnit\Framework\TestCase
{

    /**
     * @test
     */
    public function testSimpleRequest()
    {
        $url = new Url('httpbin.org', 80);

        $http = new HttpClient($url);
        $http->setTimeout(2);
        $http->request('GET');

        $this->assertEquals(200, $http->getResponseCode());

        $response = "";

        while ($http->getPayload()->ready()) {
            $response = $http->getPayload()->read(
                $http->getPayload()
                ->count()
            );
        }

        $this->assertNotEmpty($response);
    }

    /**
     * @test
     * @expectedException Generics\Socket\SocketException
     * @expectedException Socket is not available
     */
    public function testRequestAfterClose()
    {
        $url = new Url('httpbin.org', 80);
        
        $http = new HttpClient($url);
        $http->setTimeout(2);
        
        $http->close();
        
        $http->request('GET');
    }
    
    /**
     * @test
     */
    public function testRetrieveHeaders()
    {
        $url = new Url('httpbin.org', 80);

        $http = new HttpClient($url);
        $http->setHeader('Connection', '');
        $http->setTimeout(5);

        $headers = $http->retrieveHeaders();

        $this->assertEquals(200, $http->getResponseCode());

        $headers = $http->getHeaders();

        $this->assertGreaterThan(0, $headers['Content-Length']);

        $http->resetHeaders();

        $this->assertEmpty($http->getHeaders());
    }

    /**
     * @test
     */
    public function testTimeoutInvalid()
    {
        $url = new Url('httpbin.org', 80);

        $http = new HttpClient($url);
        $http->setTimeout(100);
        $http->request('GET');

        $this->assertEquals(200, $http->getResponseCode());
    }

    /**
     * @test
     */
    public function testTheRest()
    {
        $url = new Url('httpbin.org', 80);

        $http = new HttpClient($url);
        $http->setTimeout(1);
        $http->request('HEAD');

        $this->assertEquals(200, $http->getResponseCode());
    }

    /**
     * @test
     */
    public function testConnectionClose()
    {
        $url = new Url('httpbin.org', 80);

        $http = new HttpClient($url);
        $http->setHeader('Connection', 'close');

        $headers = $http->getHeaders();
        $this->assertEquals('close', $headers['Connection']);

        $http->connect();
        $this->assertTrue($http->isConnected());

        $http->request('GET');

        $this->assertFalse($http->isConnected());
    }

    /**
     * @test
     * @expectedException \Generics\Client\HttpException
     */
    public function testDelay()
    {
        $url = UrlParser::parseUrl("http://httpbin.org/delay/4");
        $http = new HttpClient($url);
        $http->setTimeout(2);

        $http->request('GET');
    }

    /**
     * @test
     */
    public function testSendPayload()
    {
        $url = new Url('httpbin.org', 80);
        $http = new HttpClient($url);

        $input = new MemoryStream();
        $input->write("Hello Server");

        $http->appendPayload($input);

        $http->request('GET');

        $this->assertEquals(200, $http->getResponseCode());

        $http->disconnect();
    }

    /**
     * @test
     * @expectedException \Generics\Client\HttpException
     */
    public function testSSLConnection()
    {
        $url = UrlParser::parseUrl('https://httpbin.org/');
        $http = new HttpClient($url);
        $http->request('GET');
    }
    
    /**
     * @test
     */
    public function testQueryString()
    {
        $url = UrlParser::parseUrl('http://httpbin.org/get?foo=bar');
        $http = new HttpClient($url);
        $http->request('GET');
        
        $this->assertEquals(200, $http->getResponseCode());
        
        $response = "";
        
        while ($http->getPayload()->ready()) {
            $response = $http->getPayload()->read(
                $http->getPayload()
                ->count()
                );
        }
        
        $this->assertNotEmpty($response);
        $this->assertContains('"foo": "bar"', $response);
    }
    
    /**
     * @test
     */
    public function testGzip()
    {
        $url = UrlParser::parseUrl('http://httpbin.org/gzip');
        $http = new HttpClient($url);
        $http->request('GET');
        
        $this->assertEquals(200, $http->getResponseCode());
        
        $response = "";
        
        while ($http->getPayload()->ready()) {
            $response = $http->getPayload()->read(
                $http->getPayload()
                ->count()
                );
        }
        
        $this->assertNotEmpty($response);
        $this->assertJson($response);
    }
    
    /**
     * @test
     */
    public function testDeflate()
    {
        $url = UrlParser::parseUrl('http://httpbin.org/deflate');
        $http = new HttpClient($url);
        $http->request('GET');
        
        $this->assertEquals(200, $http->getResponseCode());
        
        $response = "";
        
        while ($http->getPayload()->ready()) {
            $response = $http->getPayload()->read(
                $http->getPayload()
                ->count()
                );
        }
        
        $this->assertNotEmpty($response);
        $this->assertJson($response);
    }
    
}
