<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams;

use Generics\GenericsException;

/**
 * Derived exception
 *
 * Will be thrown whenever the stream is used wrong (e.g. read/write after close, etc.)
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class StreamException extends GenericsException
{
}
