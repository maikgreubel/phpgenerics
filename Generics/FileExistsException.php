<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics;

/**
 * Derived exception
 *
 * Will be thrown whenever the given file already exists
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class FileExistsException extends \Exception
{
  /**
   * Create a new FileExistsException
   *
   * @param string $message
   *          The message to throw
   */
  public function __construct($message)
  {
    parent::__construct ( $message, 0, null );
  }
}