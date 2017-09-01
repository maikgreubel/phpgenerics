<?php
/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Client;

use Generics\Streams\InputOutputStream;
use Generics\Streams\InputStream;
use Generics\Streams\MemoryStream;

/**
 * This trait provides common http(s) client functionality
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
trait HttpClientTrait
{
    use HttpHeadersTrait;

    /**
     * The query string
     *
     * @var string
     */
    private $queryString;

    /**
     * The payload
     *
     * @var MemoryStream
     */
    private $payload;

    /**
     * The HTTP protocol version
     *
     * @var string
     */
    private $protocol;

    /**
     * Path to file on server (excluding endpoint address)
     *
     * @var string
     */
    private $path;

    /**
     * When the connection times out (in seconds)
     *
     * @var int
     */
    private $timeout;
    
    /**
     * Load headers from remote and return it
     *
     * @return array
     */
    public function retrieveHeaders(): array
    {
        $this->setHeader('Connection', 'close');
        $this->setHeader('Accept', '');
        $this->setHeader('Accept-Language', '');
        $this->setHeader('User-Agent', '');
        
        $savedProto = $this->protocol;
        $this->protocol = 'HTTP/1.0';
        $this->request('HEAD');
        $this->protocol = $savedProto;
        
        return $this->getHeaders();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\HttpStream::appendPayload()
     */
    public function appendPayload(InputStream $payload)
    {
        while ($payload->ready()) {
            $this->payload->write($payload->read(1024));
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\HttpStream::getPayload()
     */
    public function getPayload(): InputOutputStream
    {
        return $this->payload;
    }

    /**
     * Set connection timeout in seconds
     *
     * @param int $timeout
     */
    public function setTimeout($timeout)
    {
        $timeout = intval($timeout);
        if ($timeout < 1 || $timeout > 60) {
            $timeout = 5;
        }
        $this->timeout = $timeout;
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Generics\Resettable::reset()
     */
    public function reset()
    {
        if (null == $this->payload) {
            $this->payload = new MemoryStream();
        }
        $this->payload->reset();
    }

    /**
     * Prepare the request buffer
     *
     * @param string $requestType
     * @return \Generics\Streams\MemoryStream
     * @throws \Generics\Streams\StreamException
     */
    private function prepareRequest($requestType): MemoryStream
    {
        $ms = new MemoryStream();
        
        // First send the request type
        $ms->interpolate("{rqtype} {path}{query} {proto}\r\n", array(
            'rqtype' => $requestType,
            'path' => $this->path,
            'proto' => $this->protocol,
            'query' => (strlen($this->queryString) ? '?' . $this->queryString : '')
        ));
        
        // Add the host part
        $ms->interpolate("Host: {host}\r\n", array(
            'host' => $this->getEndpoint()
                ->getAddress()
        ));
        
        $this->adjustHeaders($requestType);
        
        // Add all existing headers
        foreach ($this->getHeaders() as $headerName => $headerValue) {
            if (isset($headerValue) && strlen($headerValue) > 0) {
                $ms->interpolate("{headerName}: {headerValue}\r\n", array(
                    'headerName' => $headerName,
                    'headerValue' => $headerValue
                ));
            }
        }
        
        $ms->write("\r\n");
        
        return $ms;
    }

    /**
     * Set the query string
     *
     * @param string $queryString
     */
    public function setQueryString(string $queryString)
    {
        $this->queryString = $queryString;
    }

    /**
     * Retrieve and parse the response
     *
     * @param string $requestType
     * @throws \Generics\Client\HttpException
     * @throws \Generics\Socket\SocketException
     * @throws \Generics\Streams\StreamException
     */
    private function retrieveAndParseResponse($requestType)
    {
        $this->payload = new MemoryStream();
        $this->headers = array();
        
        $delimiterFound = false;
        
        $tmp = "";
        $numBytes = 1;
        $start = time();
        while (true) {
            if (! $this->checkConnection($start)) {
                continue;
            }
            
            $c = $this->read($numBytes);
            
            if ($c == null) {
                break;
            }
            
            $start = time(); // we have readen something => adjust timeout start point
            $tmp .= $c;
            
            if (! $delimiterFound) {
                $this->handleHeader($delimiterFound, $numBytes, $tmp);
            }
            
            if ($delimiterFound) {
                if ($requestType == 'HEAD') {
                    // Header readen, in type HEAD it is now time to leave
                    break;
                }
                
                // delimiter already found, append to payload
                $this->payload->write($tmp);
                $tmp = "";
                
                if ($this->checkContentLengthExceeded()) {
                    break;
                }
            }
        }
        
        // Set pointer to start
        $this->payload->reset();
        
        $mayCompressed = $this->payload->read($this->payload->count());
        switch ($this->getContentEncoding()) {
            case 'gzip':
                $uncompressed = gzdecode(strstr($mayCompressed, "\x1f\x8b"));
                $this->payload->flush();
                $this->payload->write($uncompressed);
                break;
            
            case 'deflate':
                $uncompressed = gzuncompress($mayCompressed);
                $this->payload->flush();
                $this->payload->write($uncompressed);
                break;
            
            default:
                // nothing
                break;
        }
        $this->payload->reset();
    }

    /**
     * Append the payload buffer to the request buffer
     *
     * @param MemoryStream $ms
     * @return MemoryStream
     * @throws \Generics\Streams\StreamException
     * @throws \Generics\ResetException
     */
    private function appendPayloadToRequest(MemoryStream $ms): MemoryStream
    {
        $this->payload->reset();
        
        while ($this->payload->ready()) {
            $ms->write($this->payload->read(1024));
        }
        
        $ms->reset();
        
        return $ms;
    }

    /**
     * Handle a header line
     *
     * All parameters by reference, which means the the values can be
     * modified during execution of this method.
     *
     * @param boolean $delimiterFound
     *            Whether the delimiter for end of header section was found
     * @param int $numBytes
     *            The number of bytes to read from remote
     * @param string $tmp
     *            The current readen line
     */
    private function handleHeader(&$delimiterFound, &$numBytes, &$tmp)
    {
        if ($tmp == "\r\n") {
            $numBytes = $this->adjustNumbytes($numBytes);
            $delimiterFound = true;
            $tmp = "";
            return;
        }
        
        if (substr($tmp, - 2, 2) == "\r\n") {
            $this->addParsedHeader($tmp);
            $tmp = "";
        }
    }

    /**
     * Perform the request
     *
     * @param string $requestType
     */
    private function requestImpl(string $requestType)
    {
        if ($requestType == 'HEAD') {
            $this->setTimeout(1); // Don't wait too long on simple head
        }
        
        $ms = $this->prepareRequest($requestType);
        
        $ms = $this->appendPayloadToRequest($ms);
        
        if (! $this->isConnected()) {
            $this->connect();
        }
        
        while ($ms->ready()) {
            $this->write($ms->read(1024));
        }
        
        $this->retrieveAndParseResponse($requestType);
        
        if ($this->getHeader('Connection') == 'close') {
            $this->disconnect();
        }
    }

    /**
     * Check the connection availability
     *
     * @param int $start
     *            Timestamp when read request attempt starts
     * @throws HttpException
     * @return bool
     */
    private function checkConnection($start): bool
    {
        if (! $this->ready()) {
            if (time() - $start > $this->timeout) {
                $this->disconnect();
                throw new HttpException("Connection timed out!");
            }
            
            return false;
        }
        
        return true;
    }

    /**
     * Check whether the readen bytes amount has reached the
     * content length amount
     *
     * @return bool
     */
    private function checkContentLengthExceeded(): bool
    {
        if (isset($this->headers['Content-Length'])) {
            if ($this->payload->count() >= $this->headers['Content-Length']) {
                return true;
            }
        }
        return false;
    }

    /**
     * Set the used protocol
     *
     * @param string $protocol
     */
    private function setProtocol(string $protocol)
    {
        $this->protocol = $protocol;
    }

    /**
     * Set the path on remote server
     * 
     * @param string $path
     */
    private function setPath(string $path)
    {
        $this->path = $path;
    }
}