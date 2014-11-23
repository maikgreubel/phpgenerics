<?php

namespace Generics\Tests;

use Generics\Socket\ServiceCallback;
use Generics\Socket\Socket;

class TestServiceCallback extends ServiceCallback
{
    public function callback(Socket $client)
    {
        return false;
    }
}
