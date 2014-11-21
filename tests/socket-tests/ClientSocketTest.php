<?php
require_once 'Generics/Socket/ClientSocket.php';
require_once 'Generics/Socket/Endpoint.php';
require_once 'Generics/Socket/SocketException.php';

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
  
  /**
   * @expectedException \Generics\Socket\SocketException
   */
  public function testClientSocketConnectionFailed()
  {
    $client = new ClientSocket( new Endpoint('127.0.0.1', 5555) );
    $client->connect();
  }
}