<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Socket;

use Generics\GenericsException;

/**
 * Derived exception
 *
 * Will be thrown whenever socket has an exceptional state
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class SocketException extends GenericsException
{

    /**
     * Create a new SocketException
     *
     * @param string $message
     *            The message to throw
     * @param
     *            int code
     *            The code
     */
    public function __construct($message, $code = 0)
    {
        parent::__construct(trim($message), array(), $code, null);
    }
}
