<?php
require 'Generics/Socket/ClientSocket.php';
require 'Generics/Socket/Endpoint.php';

use Generics\Socket\ClientSocket;
use Generics\Socket\Endpoint;

class ClientSocketTest extends PHPUnit_Framework_TestCase
{
  public function testClientSocketConnect()
  {
    $client = new ClientSocket ( new Endpoint('nkey.de', 80) );
    $client->connect ();
    $this->assertTrue ( $client->ready () );
    $client->close ();
    $this->assertFalse ( $client->ready () );
  }
}