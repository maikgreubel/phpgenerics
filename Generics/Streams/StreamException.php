<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams;

/**
 * Derived exception
 *
 * Will be thrown whenever the stream is used wrong (e.g. read/write after close, etc.)
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class StreamException extends \Exception
{
  /**
   * Create a new StreamException
   *
   * @param string $message
   *          The message to throw
   */
  public function __construct($message)
  {
    parent::__construct ( $message, 0, null );
  }
}