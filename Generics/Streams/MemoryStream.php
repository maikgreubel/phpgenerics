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
require_once 'Generics/Streams/InputOutputStream.php';
require_once 'Generics/Streams/StreamException.php';

/**
 * This class provides a memory stream for both input and output
 * 
 * @author Maik Greubel <greubel@nkey.de>
 */
class MemoryStream implements InputOutputStream
{
  /**
   * The local memory buffer
   * 
   * @var string
   */
  private $memory;
  
  /**
   * Current position in memory buffer
   * 
   * @var int
   */
  private $current;
  
  /**
   * Whether it is possible to perform reading action
   * 
   * @var boolean
   */
  private $ready;
  
  /**
   * Whether stream is closed
   * 
   * @var boolean
   */
  private $closed;
  
  /**
   * Create a new MemoryStream
   * 
   * @param InputStream $in optional existing input stream - will be copied
   */
  public function __construct(InputStream $in = null)
  {
    $this->memory = "";
    if($in != null)
    {
      $copy = clone $in;
      $copy->reset();
      while($copy->ready())
      {
        $this->memory .= $copy->read();
      }
      $copy->close();
    }
    $this->current = 0;
    $this->ready = true;
    $this->closed = false;
  }
  
  /**
   * (non-PHPdoc)
   * @see \Generics\Streams\Stream::close()
   */
  public function close()
  {
    unset($this->memory);
    $this->current = 0;
    $this->ready = false;
    $this->closed = true;
  }
  
  /**
   * (non-PHPdoc)
   * @see \Generics\Streams\Stream::ready()
   */
  public function ready()
  {
    return $this->ready;
  }
  
  /**
   * (non-PHPdoc)
   * @see \Generics\Streams\OutputStream::write()
   */
  public function write($buffer)
  {
    if($this->closed)
    {
      throw new StreamException("Stream is not open");
    }
    $this->memory .= $buffer;
    $this->ready = true;
  }
  
  /**
   * (non-PHPdoc)
   * @see \Generics\Streams\InputStream::read()
   */
  public function read($length = 1)
  {
    if($this->closed)
    {
      throw new StreamException("Stream is not open");
    }
    
    if(strlen($this->memory) <= $this->current)
    {
      $this->ready = false;
      return "";
    }
    
    if(strlen($this->memory) - $this->current < $length)
    {
      $length = strlen($this->memory) - $this->current;
    }
    
    $out = substr($this->memory, $this->current, $length);
    $this->current+=$length;
    
    return $out;
  }
  
  /**
   * (non-PHPdoc)
   * @see \Generics\Streams\Stream::size()
   */
  public function size()
  {
    if($this->closed)
    {
      throw new StreamException("Stream is not open");
    }
    return strlen($this->memory);
  }
  
  /**
   * (non-PHPdoc)
   * @see \Generics\Streams\InputStream::reset()
   */
  public function reset()
  {
    if($this->closed)
    {
      throw new StreamException("Stream is not open");
    }
    $this->current = 0;
    $this->ready = true;
  }
}