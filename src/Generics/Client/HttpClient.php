<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Client;

use Generics\Streams\HttpStream;
use Generics\Streams\InputStream;
use Generics\Streams\MemoryStream;
use Generics\Socket\ClientSocket;
use Generics\Socket\Url;
use Generics\Util\Arrays;
use Generics\Streams\InputOutputStream;

/**
 * This class implements a HttpStream as client
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class HttpClient extends ClientSocket implements HttpStream
{

    /**
     * Path to file on server (excluding endpoint address)
     *
     * @var string
     */
    private $path;

    /**
     * Headers
     *
     * @var array
     */
    private $headers;

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
     * Whether to use https instead of http
     *
     * @var boolean
     */
    private $secure;

    /**
     * The response status code
     *
     * @var int
     */
    private $responseCode;

    /**
     * When the connection times out (in seconds)
     *
     * @var int
     */
    private $timeout;

    /**
     * The query string
     *
     * @var string
     */
    private $queryString;

    /**
     * Create a new http client
     *
     * @param Url $url
     *            The url for http request
     * @param string $proto
     *            The protocol to use (default = HTTP/1.1)
     * @param integer $timeout
     *            Optional timeout for request (default = 10 seconds)
     */
    public function __construct(Url $url, $proto = 'HTTP/1.1', $timeout = 10)
    {
        parent::__construct($url);
        $this->path = $url->getPath();
        $this->queryString = $url->getQueryString();
        $this->secure = $url->getScheme() == 'https';
        $this->protocol = $proto;
        $this->headers = array();
        $this->payload = new MemoryStream();
        $this->timeout = $timeout;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\HttpStream::getHeaders()
     */
    public function getHeaders(): array
    {
        return $this->headers;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\HttpStream::setHeader()
     * @return HttpClient
     */
    public function setHeader($headerName, $headerValue)
    {
        $this->headers[$headerName] = $headerValue;
    }

    /**
     * Reset the headers
     *
     * @return HttpClient
     */
    public function resetHeaders(): HttpClient
    {
        $this->headers = array();
        return $this;
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
        
        return $this->headers;
    }

    /**
     * Set connection timeout in seconds
     *
     * @param int $timeout
     * @return HttpClient
     */
    public function setTimeout($timeout): HttpClient
    {
        $timeout = intval($timeout);
        if ($timeout < 1 || $timeout > 60) {
            $timeout = 5;
        }
        $this->timeout = $timeout;
        return $this;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\HttpStream::request()
     */
    public function request($requestType)
    {
        if ($this->secure) {
            throw new HttpException("Secure connection using HTTPs is not supported yet!");
        }
        
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
        
        if ($this->headers['Connection'] == 'close') {
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
     * Adjust number of bytes to read according content length header
     *
     * @param int $numBytes
     * @return int
     */
    private function adjustNumbytes($numBytes): int
    {
        if (isset($this->headers['Content-Length'])) {
            // Try to read the whole payload at once
            $numBytes = intval($this->headers['Content-Length']);
        }
        
        return $numBytes;
    }

    /**
     * Try to parse line as header and add the results to local header list
     *
     * @param string $line
     */
    private function addParsedHeader($line)
    {
        if (strpos($line, ':') === false) {
            $this->responseCode = HttpStatus::parseStatus($line)->getCode();
        } else {
            $line = trim($line);
            list ($headerName, $headerValue) = explode(':', $line, 2);
            $this->headers[$headerName] = trim($headerValue);
        }
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
        
        if (Arrays::hasElement($this->headers, 'Content-Encoding')) {
            $mayCompressed = $this->payload->read($this->payload->count());
            switch ($this->headers['Content-Encoding']) {
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
        }
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
        foreach ($this->headers as $headerName => $headerValue) {
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
     * Depending on request type the connection header is either
     * set to keep-alive or close
     *
     * @param string $requestType
     */
    private function adjustConnectionHeader($requestType)
    {
        if ($requestType == 'HEAD') {
            $this->setHeader('Connection', 'close');
        } else {
            $this->setHeader('Connection', 'keep-alive');
        }
    }

    /**
     * Adjust the headers by injecting default values for missing keys.
     */
    private function adjustHeaders($requestType)
    {
        if (! array_key_exists('Accept', $this->headers) && $requestType != 'HEAD') {
            $this->setHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
        }
        
        if (! array_key_exists('Accept-Language', $this->headers) && $requestType != 'HEAD') {
            $this->setHeader('Accept-Language', 'en-US;q=0.7,en;q=0.3');
        }
        
        if (! array_key_exists('User-Agent', $this->headers) && $requestType != 'HEAD') {
            $this->setHeader('User-Agent', 'phpGenerics 1.0');
        }
        
        if (! array_key_exists('Connection', $this->headers) || strlen($this->headers['Connection']) == 0) {
            $this->adjustConnectionHeader($requestType);
        }
        
        if (! array_key_exists('Accept-Encoding', $this->headers)) {
            $encoding = "";
            if (function_exists('gzinflate')) {
                $encoding = 'gzip, deflate';
            } else {
                $encoding = 'identity';
            }
            $this->setHeader('Accept-Encoding', $encoding);
        }
    }

    /**
     * Retrieve the response status code
     *
     * @return int
     */
    public function getResponseCode(): int
    {
        return $this->responseCode;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\Stream::isOpen()
     */
    public function isOpen(): bool
    {
        return true;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Resettable::reset()
     */
    public function reset()
    {
        $this->payload->reset();
    }
}
