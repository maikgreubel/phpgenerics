<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Socket;

/**
 * Import dependencies
 */
require_once 'Generics/Streams/SocketStream.php';

use Generics\Streams\SocketStream;

/**
 * This abstract class provides basic socket functionality
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
abstract class Socket implements SocketStream
{
  /**
   * The socket handle
   *
   * @var resource
   */
  protected $handle;
  
  /**
   * The socket endpoint
   *
   * @var Endpoint
   */
  protected $endpoint;
  
  /**
   * Create a new socket
   *
   * @param Endpoint $endpoint
   *          The endpoint for the socket
   */
  public function __construct(Endpoint $endpoint)
  {
    $this->endpoint = $endpoint;
    $this->handle = socket_create ( AF_INET, SOCK_STREAM, SOL_TCP );
  }
  
  /**
   * (non-PHPdoc)
   *
   * @see \Generics\Streams\Stream::close()
   */
  public function close()
  {
    if (is_resource ( $this->handle ))
    {
      socket_close ( $this->handle );
      $this->handle = null;
    }
  }
  
  /**
   * (non-PHPdoc)
   *
   * @see \Generics\Streams\Stream::ready()
   */
  public function ready()
  {
    return is_resource ( $this->handle );
  }
  
  /**
   * (non-PHPdoc)
   *
   * @see \Countable::count()
   */
  public function count()
  {
    throw new SocketException ( "Cannot count elements of socket" );
  }
  
  /**
   * (non-PHPdoc)
   *
   * @see \Generics\Streams\InputStream::read()
   */
  public function read($length = 1)
  {
    if (($buf = socket_read ( $this->handle, $length )) === false)
    {
      $code = socket_last_error ();
      throw new SocketException ( socket_strerror ( $code ), $code );
    }
  }
  
  /**
   * (non-PHPdoc)
   *
   * @see \Generics\Streams\OutputStream::write()
   */
  public function write($buffer)
  {
    if (($written = socket_write ( $this->handle, $buffer )) === false)
    {
      $code = socket_last_error ();
      throw new SocketException ( socket_strerror ( $code ), $code );
    }
    
    if ($written != strlen ( $buffer ))
    {
      throw new SocketException ( "Could not write all bytes to socket" );
    }
  }
}