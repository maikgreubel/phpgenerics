<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Socket;

use Generics\GenericsException;
use Exception;

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
     *            The message to throw; May contain placeholder like {placeholder} and will be replaced by context
     *            elements
     * @param array $context
     *            The context elements to replace in message
     * @param number $code
     *            Optional code
     * @param Exception $previous
     *            Optional previous exception
     */
    public function __construct($message, array $context = array(), $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $context, $code, $previous);
    }
}
