<?php
namespace Generics\Tests;

use Generics\Client\HttpsClient;
use Generics\Socket\Url;
use Generics\Streams\MemoryStream;
use Generics\Util\UrlParser;
use PHPUnit\Framework\TestCase;

class HttpsClientTest extends TestCase
{
    public function testSimpleHttps()
    {
        $https = new HttpsClient(new Url("https://httpbin.org/get"));
        $https->setQueryString("foo=bar");

        $https->request("GET");

        $this->assertEquals(200, $https->getResponseCode());

        $response = "";

        while ($https->getPayload()->ready()) {
            $response = $https->getPayload()->read($https->getPayload()
                ->count());
        }

        $this->assertNotEmpty($response);
    }

    /**
     * @expectedException Generics\Socket\SocketException
     * @expectedExceptionMessage Stream is not ready for writing
     */
    public function testRequestAfterClose()
    {
        $https = new HttpsClient(new Url("https://httpbin.org/get"));
        $https->setQueryString("foo=bar");

        $https->close();
        $https->request("GET");
    }

    public function testQueryString()
    {
        $url = UrlParser::parseUrl('https://httpbin.org/get?foo=bar');
        $https = new HttpsClient($url);
        $https->request('GET');

        $this->assertEquals(200, $https->getResponseCode());

        $response = "";

        while ($https->getPayload()->ready()) {
            $response = $https->getPayload()->read(
                $https->getPayload()
                ->count()
            );
        }

        $this->assertNotEmpty($response);
        $this->assertContains('"foo": "bar"', $response);
    }

    public function testRetrieveHeaders()
    {
        $url = new Url('httpbin.org', 443);

        $https = new HttpsClient($url);
        $https->setHeader('Connection', '');
        $https->setTimeout(5);

        $headers = $https->retrieveHeaders();

        $this->assertEquals(200, $https->getResponseCode());

        $headers = $https->getHeaders();

        $this->assertGreaterThan(0, $headers['Content-Length']);

        $https->resetHeaders();

        $this->assertEmpty($https->getHeaders());
    }

    public function testTimeoutInvalid()
    {
        $url = new Url('httpbin.org', 443);

        $https = new HttpsClient($url);
        $https->setTimeout(100);
        $https->request('GET');

        $this->assertEquals(200, $https->getResponseCode());
    }

    public function testTheRest()
    {
        $url = new Url('httpbin.org', 443);

        $https = new HttpsClient($url);
        $https->setTimeout(1);
        $https->request('HEAD');

        $this->assertEquals(200, $https->getResponseCode());
    }

    public function testConnectionClose()
    {
        $url = new Url('httpbin.org', 443);

        $https = new HttpsClient($url);
        $https->setHeader('Connection', 'close');

        $headers = $https->getHeaders();
        $this->assertEquals('close', $headers['Connection']);

        $https->connect();
        $this->assertTrue($https->isConnected());

        $https->request('GET');

        $this->assertFalse($https->isConnected());
    }

    /**
     * @expectedException \Generics\Client\HttpException
     */
    public function testDelay()
    {
        $url = UrlParser::parseUrl("https://httpbin.org/delay/4");
        $https = new HttpsClient($url);
        $https->setTimeout(2);

        $https->request('GET');
    }

    public function testSendPayload()
    {
        $url = new Url('httpbin.org', 443);
        $https = new HttpsClient($url);

        $input = new MemoryStream();
        $input->write("Hello Server");

        $https->appendPayload($input);

        $https->request('GET');

        $this->assertEquals(200, $https->getResponseCode());

        $https->disconnect();
    }

    public function testGzip()
    {
        $url = UrlParser::parseUrl('https://httpbin.org/gzip');
        $https = new HttpsClient($url);
        $https->request('GET');

        $this->assertEquals(200, $https->getResponseCode());

        $response = "";

        while ($https->getPayload()->ready()) {
            $response = $https->getPayload()->read(
                $https->getPayload()
                ->count()
            );
        }

        $this->assertNotEmpty($response);
        $this->assertJson($response);
    }

    public function testDeflate()
    {
        $url = UrlParser::parseUrl('https://httpbin.org/deflate');
        $https = new HttpsClient($url);
        $https->request('GET');

        $this->assertEquals(200, $https->getResponseCode());

        $response = "";

        while ($https->getPayload()->ready()) {
            $response = $https->getPayload()->read(
                $https->getPayload()
                ->count()
            );
        }

        $this->assertNotEmpty($response);
        $this->assertJson($response);
    }
}
