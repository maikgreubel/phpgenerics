<?php
/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */

namespace Generics\Streams;

/**
 * This interface describes the implementation of a stream.
 * 
 * @author Maik
 */
interface Stream
{
  /**
   * Close the stream.
   * After closing the stream will be no longer available for reading and writing.
   */
  public function close();
  
  /**
   * Checks whether stream is ready for action.
   * @return boolean
   */
  public function ready();
  
  /**
   * Retrieve the size of stream.
   * 
   * @return int
   * @throws StreamException in case of stream is closed
   */
  public function size();
}