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
}
