<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams;

/**
 * Import dependencies
 */
require_once 'Generics/Streams/Stream.php';

/**
 * This interface describes the implementation of an input stream.
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
interface InputStream extends Stream
{

    /**
     * Read bytes from stream.
     *
     * @param int $length
     *            Number of bytes to read
     * @throws StreamException in case of stream is closed
     * @return string The readen buffer
     */
    public function read($length);
}