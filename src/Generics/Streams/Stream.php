<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams;

use \Countable;
use Generics\Resettable;

/**
 * This interface describes the implementation of a stream.
 *
 * @author Maik
 */
interface Stream extends Countable, Resettable
{

    /**
     * Close the stream.
     * After closing the stream will be no longer available for reading and writing.
     */
    public function close();

    /**
     * Checks whether stream is ready for action.
     *
     * @return bool
     */
    public function ready(): bool;

    /**
     * Retrieves open status of stream
     *
     * @return bool
     */
    public function isOpen(): bool;
}
