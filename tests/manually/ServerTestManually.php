<?php
set_include_path(get_include_path() . PATH_SEPARATOR . '../../');

require_once 'Generics/Socket/Socket.php';
require_once 'Generics/Socket/ServerSocket.php';
require_once 'Generics/Socket/ServiceCallback.php';
require_once 'Generics/Socket/Endpoint.php';
require_once 'Generics/Socket/SocketException.php';

require './TestServerSocketCallback.php';

use Generics\Socket\Socket;
use Generics\Socket\ServerSocket;
use Generics\Socket\ServiceCallback;
use Generics\Socket\Endpoint;

use tests\manually\TestServerSocketCallback;

$serverEndpoint = new Endpoint('127.0.0.1', 8421);
$server = new ServerSocket($serverEndpoint);
$server->serve(new TestServerSocketCallback($serverEndpoint));
