<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Socket;

class Endpoint
{
  /**
   * The address for the socket
   *
   * @var string
   */
  private $address;
  
  /**
   * The port for the socket
   *
   * @var int
   */
  private $port;
  
  /**
   * Create a new endpoint
   *
   * @param string $address
   *          The address for the socket
   * @param int $port
   *          The port for the socket
   */
  public function __construct($address, $port)
  {
    $this->address = strval($address);
    $this->port = intval($port);
  }
  
  /**
   * Retrieve the address of the endpoint
   * 
   * @return string The address
   */
  public function getAddress()
  {
    return $this->address;
  }
  
  /**
   * Retrieve the port of the endpoint
   * 
   * @return int The port
   */
  public function getPort()
  {
    return $this->port;
  }
}