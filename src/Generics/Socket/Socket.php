<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Socket;

use Generics\Streams\SocketStream;
use Generics\ResetException;
use Exception;

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
     *            The endpoint for the socket
     */
    public function __construct(Endpoint $endpoint)
    {
        $this->endpoint = $endpoint;
        $this->open();
    }
    
    /**
     * Opens a socket
     * 
     * @throws SocketException
     */
    private function open()
    {
    	$this->handle = @socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    	
    	if (! is_resource($this->handle)) {
    		$code = socket_last_error();
    		throw new SocketException(socket_strerror($code), array(), $code);
    	}
    }

    /**
     * Clean up
     */
    public function __destruct()
    {
        $this->close();
    }

    /**
     * {@inheritDoc}
     * @see \Generics\Streams\Stream::close()
     */
    public function close()
    {
        if (is_resource($this->handle)) {
            socket_close($this->handle);
            $this->handle = null;
        }
    }

    /**
     * {@inheritDoc}
     * @see \Generics\Streams\Stream::ready()
     */
    public function ready()
    {
        if (! is_resource($this->handle)) {
            return false;
        }

        $read = array(
            $this->handle
        );
        $write = null;
        $except = null;

        $num = @socket_select($read, $write, $except, 0);

        if ($num === false) {
            $code = socket_last_error($this->handle);
            throw new SocketException(socket_strerror($code), array(), $code);
        }

        if ($num < 1) {
            return false;
        }

        if (! in_array($this->handle, $read)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Generics\Streams\OutputStream::isWriteable()
     */
    public function isWriteable()
    {
        if (! is_resource($this->handle)) {
            return false;
        }

        $read = null;
        $write = array(
            $this->handle
        );
        $except = null;

        $num = @socket_select($read, $write, $except, 0, 0);

        if ($num === false) {
            $code = socket_last_error($this->handle);
            throw new SocketException(socket_strerror($code), array(), $code);
        }

        if ($num < 1) {
            return false;
        }

        if (! in_array($this->handle, $write)) {
            return false;
        }

        return true;
    }

    /**
     * {@inheritDoc}
     * @see \Countable::count()
     */
    public function count()
    {
        throw new SocketException("Cannot count elements of socket");
    }

    /**
     * {@inheritDoc}
     * @see \Generics\Streams\InputStream::read()
     */
    public function read($length = 1, $offset = null)
    {
        if (($buf = @socket_read($this->handle, $length)) === false) {
            $buf = null;
            $code = socket_last_error();
            if ($code != 0) {
                if ($code != 10053) {
                	throw new SocketException(socket_strerror($code), array(), $code);
                } else {
                    $this->handle = null;
                }
            }
        }

        return $buf;
    }

    /**
     * {@inheritDoc}
     * @see \Generics\Streams\OutputStream::write()
     */
    public function write($buffer)
    {
        if (($written = @socket_write($this->handle, "{$buffer}\0")) === false) {
            $code = socket_last_error();
            throw new SocketException(socket_strerror($code), array(), $code);
        }

        if ($written != strlen($buffer) + 1) {
            throw new SocketException("Could not write all {bytes} bytes to socket ({written} written)", array(
                'bytes' => strlen($buffer),
                'written' => $written
            ));
        }
    }

    /**
     * Get the socket endpoint
     *
     * @return \Generics\Socket\Endpoint
     */
    public function getEndpoint()
    {
        return $this->endpoint;
    }

    /**
     * {@inheritDoc}
     * @see \Generics\Streams\OutputStream::flush()
     */
    public function flush()
    {
        // There is no function to flush a socket. This is only possible for file descriptors.
    }
    
    /**
     * {@inheritDoc}
     * @see \Generics\Streams\Stream::isOpen()
     */
    public function isOpen()
    {
    	return is_resource($this->handle);
    }
    
    /**
     * {@inheritDoc}
     * @see \Generics\Resettable::reset()
     */
    public function reset()
    {
    	try {
	    	$this->close();
	    	$this->open();
    	}
    	catch(Exception $ex)
    	{
    		throw new ResetException($ex->getMessage(), array(), $ex->getCode(), $ex);
    	}
    }
}
