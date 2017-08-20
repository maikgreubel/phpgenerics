<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams;

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
     * @param int $offset
     *            The offset where to start reading
     * @throws StreamException in case of stream is closed
     * @return string The readen buffer
     */
    public function read($length = 1, $offset = null): string;
}
