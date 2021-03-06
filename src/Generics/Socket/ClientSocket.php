<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Socket;

/**
 * This class provides a basic client socket implementation
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class ClientSocket extends Socket
{

    /**
     * Whether the socket is connected
     *
     * @var boolean
     */
    private $conntected;

    /**
     * Create a new client socket
     *
     * @param Endpoint $endpoint
     *            The endpoint to use
     * @param resource $clientHandle
     *            optional existing client handle
     */
    public function __construct(Endpoint $endpoint, $clientHandle = null)
    {
        $this->endpoint = $endpoint;
        $this->handle = $clientHandle;
        $this->conntected = false;
        
        if (! is_resource($clientHandle)) {
            parent::__construct($endpoint);
        }
    }

    /**
     * Connect to remote endpoint
     *
     * @throws SocketException
     */
    public function connect()
    {
        if (!is_resource($this->handle)) {
            throw new SocketException("Socket is not available");
        }
        if (! @socket_connect($this->handle, $this->endpoint->getAddress(), $this->endpoint->getPort())) {
            $code = socket_last_error($this->handle);
            throw new SocketException(socket_strerror($code), array(), $code);
        }
        $this->conntected = true;
    }

    /**
     * Disconnects the socket
     *
     * @throws SocketException
     */
    public function disconnect()
    {
        if (! $this->conntected) {
            throw new SocketException("Socket is not connected");
        }
        
        $this->close();
    }

    /**
     * Whether the client is connected
     *
     * @return bool
     */
    public function isConnected():bool
    {
        return $this->conntected;
    }

    /**
     *
     * @see \Generics\Socket\ClientSocket::disconnect()
     */
    public function close()
    {
        parent::close();
        $this->conntected = false;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Socket\Socket::isWriteable()
     */
    public function isWriteable():bool
    {
        if (! $this->isConnected()) {
            return false;
        }
        
        return parent::isWriteable();
    }
}
