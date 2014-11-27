<?php

namespace Generics\Tests;

use Generics\Client\HttpClient;
use Generics\Socket\Url;

class HttpClientTest extends \PHPUnit_Framework_TestCase
{

    public function testSimpleRequest()
    {
        $url = new Url('httpbin.org', 80);

        $http = new HttpClient($url);
        $http->setHeader('Connection', '');
        $http->setHeader('User-Agent', '');
        $http->setHeader('Accept', '');
        $http->setHeader('Accept-Language', '');
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

    public function testRetrieveHeaders()
    {
        $url = new Url('httpbin.org', 80);

        $http = new HttpClient($url);
        $http->setHeader('Connection', '');
        $http->setTimeout(1);

        $headers = $http->retrieveHeaders();

        $this->assertEquals(200, $http->getResponseCode());
    }
}
