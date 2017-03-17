<?php

namespace Generics\Tests;

use Generics\Socket\ClientSocket;
use Generics\Socket\Endpoint;

class ClientSocketTest extends \PHPUnit\Framework\TestCase
{

    public function testClientSocketConnect()
    {
        $client = new ClientSocket(new Endpoint('httpbin.org', 80));
        $this->assertFalse($client->isConnected());
        $this->assertFalse($client->isWriteable());
        $client->connect();
        $this->assertTrue($client->isConnected());
        $this->assertTrue($client->isWriteable());
        $client->close();
        $this->assertFalse($client->ready());
    }

    /**
     * @expectedException \Generics\Socket\SocketException
     */
    public function testClientSocketConnectionFailed()
    {
        $client = new ClientSocket(new Endpoint('127.0.0.1', 5555));
        $client->connect();
    }
}
