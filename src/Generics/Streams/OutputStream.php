<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams;

/**
 * This interface describes the implementation of an output stream
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
interface OutputStream extends Stream
{

    /**
     * Write a buffer into stream
     *
     * @param string|InputStream $buffer
     *            The buffer to write (or input stream to read out of and then write)
     * @throws StreamException in case of stream is closed
     */
    public function write($buffer);

    /**
     * Whether it is possible to write to stream
     *
     * @return boolean
     */
    public function isWriteable();
}
