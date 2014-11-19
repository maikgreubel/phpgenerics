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
 * Will be thrown whenever the access to a resource is prohibited.
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class NoAccessException extends \Exception
{
  /**
   * Create a new NoAccessException
   *
   * @param string $message
   *          The message to throw
   */
  public function __construct($message)
  {
    parent::__construct ( $message, 0, null );
  }
}