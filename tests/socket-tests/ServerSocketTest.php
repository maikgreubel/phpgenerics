<?php

namespace Generics\Tests;

use Generics\Socket\Endpoint;
use Generics\Socket\ServerSocket;

class ServerSocketTest extends \PHPUnit\Framework\TestCase
{

    public function testServerSocket()
    {
        $serverEndpoint = new Endpoint('127.0.0.1', 5555);
        new ServerSocket($serverEndpoint);

        // currently we are not able to test a server socket using phpunit... :-(
        // $server->serve( new TestServiceCallback( $serverEndpoint ) );
    }
}
