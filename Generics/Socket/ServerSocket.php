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
require_once 'Generics/Socket/Socket.php';

/**
 * This class provides a basic client socket implementation
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class ServerSocket extends Socket
{
  /**
   * Create a new server socket
   *
   * @param Endpoint $endpoint
   *          The endpoint to use
   *          
   * @throws SocketException in case of creation of socket has failed or socket options could not be set
   */
  public function __construct(Endpoint $endpoint)
  {
    parent::__construct ( $endpoint );
    if (! @socket_set_option ( $this->handle, SOL_SOCKET, SO_REUSEADDR, 1 ))
    {
      $code = socket_last_error ( $this->handle );
      throw new SocketException ( socket_strerror ( $code ), $code );
    }
  }
  
  /**
   * Creates a service at the given endpoint
   *
   * @throws SocketException in case of it is not possible to serve due to binding or listening error
   */
  public function serve()
  {
    $this->bind ();
    
    $this->listen ();
    
    while ( true )
    {
      $clientHandle = @socket_accept ( $this->handle );
      
      if (! is_resource ( $clientHandle ))
      {
        $code = socket_last_error ( $this->handle );
        throw new SocketException ( socket_strerror ( $code ), $code );
      }
      
      if (! @socket_getpeername ( $clientHandle, $address, $port ))
      {
        $code = socket_last_error ( $clientHandle );
        throw new SocketException ( socket_strerror ( $code ), $code );
      }
      
      $client = ClientSocket ( new Endpoint ( $address, $port ), $clientHandle );
    }
  }
  
  /**
   * Bind the server socket to the given endpoint
   *
   * @throws SocketException in case of binding has failed
   */
  private function bind()
  {
    if (! @socket_bind ( $this->handle, $this->endpoint->getAddress (), $this->endpoint->getPort () ))
    {
      $code = socket_last_error ( $this->handle );
      throw new SocketException ( socket_strerror ( $code ), $code );
    }
  }
  
  /**
   * Listen to the binded socket endpoint
   *
   * @throws SocketException in case of listening is not possible
   */
  private function listen()
  {
    if (! @socket_listen ( $this->handle, 5 ))
    {
      $code = socket_last_error ( $this->handle );
      throw new SocketException ( socket_strerror ( $code ), $code );
    }
  }
}
