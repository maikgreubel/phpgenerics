<?php
namespace Generics\Tests;

use PHPUnit\Framework\TestCase;
use Generics\Util\UrlParser;
use Generics\Client\HttpClientFactory;
use Generics\Client\HttpClient;
use Generics\Client\HttpsClient;

class HttpClientFactoryTest extends TestCase
{
    public function testHttpFactory()
    {
        $url = UrlParser::parseUrl("http://httpbin.org");
        $client = HttpClientFactory::get($url);
        $this->assertInstanceOf(HttpClient::class, $client);
    }

    public function testHttpsFactory()
    {
        $url = UrlParser::parseUrl("https://httpbin.org");
        $client = HttpClientFactory::get($url);
        $this->assertInstanceOf(HttpsClient::class, $client);
    }
}
