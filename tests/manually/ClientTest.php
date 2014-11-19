<?php
set_include_path ( get_include_path () . PATH_SEPARATOR . '../../' );

require_once 'Generics/Socket/ClientSocket.php';
require_once 'Generics/Socket/Endpoint.php';
require_once 'Generics/Socket/SocketException.php';

use Generics\Socket\ClientSocket;
use Generics\Socket\Endpoint;
use Generics\Socket\SocketException;

$clientEndpoint = new Endpoint ( '127.0.0.1', 8421 );
$client = new ClientSocket ( $clientEndpoint );
$client->connect ();

printf ( "Connected!\n" );

$client->write ( "Hello from manually test client" );

printf ( "Send message\n" );

$response = null;

if (($buf = $client->read ( 1024 )) !== null)
{
  $response = $buf;
}
  
$client->close ();

printf ( "Response from server: %s\n", $response );

