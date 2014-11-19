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
class ClientSocket extends Socket
{
  /**
   * Create a new client socket
   *
   * @param Endpoint $endpoint
   *          The endpoint to use
   * @param resource $clientHandle
   *          optional existing client handle
   */
  public function __construct(Endpoint $endpoint, $clientHandle = null)
  {
    if (! is_resource ( $clientHandle ))
    {
      parent::__construct ( $endpoint );
    }
    else
    {
      $this->endpoint = $endpoint;
      $this->handle = $clientHandle;
    }
  }
  
  /**
   * Connect to remote endpoint
   *
   * @throws SocketException
   */
  public function connect()
  {
    if (! @socket_connect ( $this->handle, $this->endpoint->getAddress (), $this->endpoint->getPort () ))
    {
      $code = socket_last_error ( $this->handle );
      throw new SocketException ( socket_strerror ( $code ), $code );
    }
  }
}