<?php
require_once 'Generics/Socket/Socket.php';
require_once 'Generics/Socket/Endpoint.php';
require_once 'Generics/Socket/ServerSocket.php';
require_once 'Generics/Socket/ServiceCallback.php';
require_once 'Generics/Socket/SocketException.php';

use Generics\Socket\Socket;
use Generics\Socket\Endpoint;
use Generics\Socket\ServerSocket;
use Generics\Socket\ServiceCallback;

class TestServiceCallback extends ServiceCallback
{
  public function callback(Socket $client)
  {
    return false;
  }
}


class ServerSocketTest extends PHPUnit_Framework_TestCase
{
  
  public function testServerSocket()
  {
    $serverEndpoint = new Endpoint('127.0.0.1', 5555);
    new ServerSocket( $serverEndpoint );

    // currently we are not able to test a server socket using phpunit... :-(
    //$server->serve( new TestServiceCallback( $serverEndpoint ) );
  }
}