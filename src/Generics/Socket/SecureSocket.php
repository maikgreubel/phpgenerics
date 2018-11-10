<?php
/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Socket;

use Composer\CaBundle\CaBundle;
use Generics\ResetException;
use Generics\Streams\SocketStream;
use Countable;
use Exception;

/**
 * This abstract class provides basic secure socket functionality
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
abstract class SecureSocket implements SocketStream
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
     * The stream context
     *
     * @var resource
     */
    private $streamContext;

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
     *
     * {@inheritdoc}
     * @see \Generics\Streams\InputStream::read()
     */
    public function read($length = 1, $offset = null): string
    {
        return stream_get_contents($this->handle, $length, $offset === null ? - 1 : intval($offset));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\Stream::isOpen()
     */
    public function isOpen(): bool
    {
        return is_resource($this->handle) && ! feof($this->handle);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\OutputStream::flush()
     */
    public function flush()
    {
        // flush not available on streams
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\Stream::ready()
     */
    public function ready(): bool
    {
        if (! is_resource($this->handle)) {
            return false;
        }
        
        $read = array(
            $this->handle
        );
        $write = null;
        $except = null;
        
        $num = @stream_select($read, $write, $except, 0);
        
        if ($num === false) {
            throw new SocketException("Could not determine the stream client status");
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
     *
     * {@inheritdoc}
     * @see Countable::count()
     */
    public function count()
    {
        $meta = stream_get_meta_data($this->handle);
        
        foreach ($meta as $data) {
            if (strstr($data, 'Content-Length:')) {
                return intval(trim(substr($data, 15)));
            }
        }
        throw new SocketException("Cannot count elements of stream client");
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Resettable::reset()
     */
    public function reset()
    {
        try {
            $this->close();
            $this->open();
        } catch (Exception $ex) {
            throw new ResetException($ex->getMessage(), array(), $ex->getCode(), $ex);
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\Stream::close()
     */
    public function close()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
            $this->handle = null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\OutputStream::write()
     */
    public function write($buffer)
    {
        if (!$this->isWriteable()) {
            throw new SocketException("Stream is not ready for writing");
        }
        $len = strlen($buffer);
        $written = 0;
        do {
            $bytes = fwrite($this->handle, $buffer);
            if ($bytes === false) {
                throw new SocketException("Could not write {len} bytes to stream (at least {written} written)", array(
                    'len' => $len,
                    'written' => $written
                ));
            }
            $written += $bytes;
        } while ($written != $len);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\OutputStream::isWriteable()
     */
    public function isWriteable(): bool
    {
        if (! is_resource($this->handle)) {
            return false;
        }
        
        $read = null;
        $write = array(
            $this->handle
        );
        $except = null;
        
        $num = @stream_select($read, $write, $except, 0, 0);
        
        if ($num === false) {
            throw new SocketException("Could not determine the stream client status");
        }
        
        if ($num < 1) {
            return false;
        }
        
        if (! in_array($this->handle, $write)) {
            return false;
        }
        
        return true;
    }

    private function open()
    {
        $this->prepareStreamContext();
        
        $this->handle = stream_socket_client(
            sprintf('ssl://%s:%d', $this->endpoint->getAddress(), $this->endpoint->getPort()), //
            $error,
            $errorString,
            2,
            STREAM_CLIENT_CONNECT,
            $this->streamContext
        );
        
        if ($error > 0) {
            throw new SocketException($errorString, array(), $error);
        }
    }

    private function prepareStreamContext()
    {
        $opts = array(
            'http' => array(
                'method' => "GET"
            )
        );
        
        $caPath = CaBundle::getSystemCaRootBundlePath();
        
        if (is_dir($caPath)) {
            $opts['ssl']['capath'] = $caPath;
        } else {
            $opts['ssl']['cafile'] = $caPath;
        }
        
        $this->streamContext = stream_context_create($opts);
    }

    /**
     * Retrieve end point object
     *
     * @return Endpoint
     */
    public function getEndPoint(): Endpoint
    {
        return $this->endpoint;
    }
}
