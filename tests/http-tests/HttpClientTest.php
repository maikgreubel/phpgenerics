<?php

namespace Generics\Tests;

use Generics\Client\HttpClient;
use Generics\Socket\Endpoint;

class HttpClientTest extends \PHPUnit_Framework_TestCase
{

    public function testSimpleRequest()
    {
        $http = new HttpClient(new Endpoint('httpbin.org', 80));
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
