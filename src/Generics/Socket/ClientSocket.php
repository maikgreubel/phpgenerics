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
        if (! @socket_connect($this->handle, $this->endpoint->getAddress(), $this->endpoint->getPort())) {
            $code = socket_last_error($this->handle);
            throw new SocketException(socket_strerror($code), $code);
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

        parent::close();
        $this->conntected = false;
    }

    /**
     * Whether the client is connected
     *
     * @return boolean
     */
    public function isConnected()
    {
        return $this->conntected;
    }

    /**
     * This method does nothing! Use disconnect() instead
     *
     * @see \Generics\Socket\ClientSocket::disconnect()
     */
    public function close()
    {
        // Do nothing, the disconnect() method has to be called
    }

    /**
     * {@inheritDoc}
     * @see \Generics\Socket\Socket::isWriteable()
     */
    public function isWriteable()
    {
        if (!$this->isConnected()) {
            return false;
        }

        return parent::isWriteable();
    }
}
