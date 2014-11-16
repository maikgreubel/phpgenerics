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
 * This interface describes the implementation of an output stream
 * 
 * @author Maik Greubel <greubel@nkey.de>
 */
interface OutputStream extends Stream
{
  /**
   * Write a buffer into stream
   * 
   * @param string $buffer The buffer to write
   * @throws StreamException in case of stream is closed
   */
  public function write($buffer);
}