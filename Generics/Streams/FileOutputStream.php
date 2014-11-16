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
require_once 'Generics/Streams/OutputStream.php';
require_once 'Generics/FileNotFoundException.php';
require_once 'Generics/NoAccessException.php';

use Generics\FileExistsException;
use Generics\NoAccessException;

/**
 * This class provides a file output stream.
 * 
 * @author Maik
 */
class FileOutputStream implements OutputStream
{
  /**
   * The file handle
   * 
   * @var resource
   */
  private $handle;
  
  /**
   * Create a new FileOutputStream
   * 
   * @param string $file The absolute (or relative) path to file to write into.
   * @param boolean $append Whether to append the data to an existing file.
   * @throws FileExistsException will be thrown in case of file exists and append is set to false.
   * @throws NoAccessException will be thrown in case of it is not possible to write to file.
   */
  public function __construct($file, $append = false)
  {
    $mode = "wb";
    
    if (file_exists ( $file ))
    {
      if (! $append)
      {
        throw new FileExistsException ( "File $file already exists!" );
      }
      
      if (! is_writable ( $file ))
      {
        throw new NoAccessException ( "Cannot write to file $file" );
      }
      $mode = "ab";
    }
    else
    {
      if (! is_writable ( dirname( $file ) ))
      {
        throw new NoAccessException ( "Cannot write to file $file" );
      }
    }
    
    $this->handle = fopen($file, $mode);
  }
  
  /**
   * (non-PHPdoc)
   * @see \Generics\Streams\Stream::ready()
   */
  public function ready()
  {
    return is_resource ( $this->handle );
  }
  
  /**
   * (non-PHPdoc)
   * @see \Generics\Streams\OutputStream::write()
   */
  public function write($buffer)
  {
    if(!$this->ready())
    {
      throw new StreamException("Stream is not open!");
    }
    
    fwrite($this->handle, $buffer);
    fflush($this->handle);
  }
  
  /**
   * (non-PHPdoc)
   * @see \Generics\Streams\Stream::close()
   */
  public function close()
  {
    if(is_resource($this->handle))
    {
      fclose($this->handle);
      $this->handle = null;
    }
  }
  
  /**
   * (non-PHPdoc)
   * @see \Generics\Streams\Stream::size()
   */
  public function size()
  {
    if(!$this->ready())
    {
      throw new StreamException("Stream is not open!");
    }
    
    return ftell($this->handle);
  }
}