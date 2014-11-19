<?php
set_include_path ( get_include_path () . PATH_SEPARATOR . '../../' );

require_once 'Generics/Socket/Socket.php';
require_once 'Generics/Socket/ServerSocket.php';
require_once 'Generics/Socket/ServiceCallback.php';
require_once 'Generics/Socket/Endpoint.php';
require_once 'Generics/Socket/SocketException.php';

use Generics\Socket\Socket;
use Generics\Socket\ServerSocket;
use Generics\Socket\ServiceCallback;
use Generics\Socket\Endpoint;


class ServerSocketCallback extends ServiceCallback
{
  public function callback(Socket $client)
  {
    //printf ( "Incoming connection from %s (remote port = %d)\n", $client->getEndpoint ()->getAddress (), $client->getEndpoint ()->getPort () );
    
    $in = null;
    if (($buf = $client->read ( 1024 )) !== null)
    {
      $in = $buf;
    }
    // Just return to sender
    $client->write ( $in );
  }
}

$serverEndpoint = new Endpoint ( '127.0.0.1', 8421 );
$server = new ServerSocket ( $serverEndpoint );
$server->serve ( new ServerSocketCallback ( $serverEndpoint ) );