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
 * Will be thrown whenever a given file does not exist actually
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class FileNotFoundException extends \Exception
{
  /**
   * Create a new FileNotFoundException
   *
   * @param string $message The message to throw
   */
  public function __construct($message)
  {
    parent::__construct ( $message, 0, null );
  }
}