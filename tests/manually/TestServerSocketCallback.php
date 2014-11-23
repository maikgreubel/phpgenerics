<?php

namespace tests\manually;

use Generics\Socket\ServiceCallback;
use Generics\Socket\Socket;

class TestServerSocketCallback extends ServiceCallback
{

    public function callback(Socket $client)
    {
        // printf ( "Incoming connection from %s (remote port = %d)\n",
        //    $client->getEndpoint ()->getAddress (), $client->getEndpoint ()->getPort () );
        $in = null;
        if (($buf = $client->read(1024)) !== null) {
            $in = $buf;
        }
        // Just return to sender
        $client->write($in);
    }
}
