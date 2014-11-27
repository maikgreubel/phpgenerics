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
use Generics\Streams\InputOutputStream;
use Generics\Socket\ClientSocket;
use Generics\Socket\Endpoint;
use Generics\Socket\Url;

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
     * Create a new http client
     *
     * @param Endpoint $endpoint
     *            The endpoint address for http request
     * @param string $path
     *            The path part for http request
     */
    public function __construct(Url $url, $proto = 'HTTP/1.1', $timeout = 5)
    {
        parent::__construct($url);
        $this->path = $url->getPath();
        $this->secure = $url->getScheme() == 'https';
        $this->protocol = $proto;
        $this->headers = array();
        $this->payload = new MemoryStream();
        $this->timeout = $timeout;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\HttpStream::getHeaders()
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\HttpStream::setHeader()
     * @return HttpClient
     */
    public function setHeader($headerName, $headerValue)
    {
        $this->headers[$headerName] = $headerValue;
        return $this;
    }

    /**
     * Reset the headers
     */
    public function resetHeaders()
    {
        $this->headers = array();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\HttpStream::appendPayload()
     */
    public function appendPayload(InputStream $payload)
    {
        while ($payload->ready()) {
            $this->payload->write($payload->read(1024));
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\HttpStream::getPayload()
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Load headers from remote and return it
     *
     * @return array
     */
    public function retrieveHeaders()
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
     * (non-PHPdoc)
     *
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

        $ms = new MemoryStream();

        // First send the request type
        $ms->interpolate("{rqtype} {path} {proto}\r\n", array(
            'rqtype' => $requestType,
            'path' => $this->path,
            'proto' => $this->protocol
        ));

        // Add the host part
        $ms->interpolate("Host: {host}\r\n", array(
            'host' => $this->getEndpoint()
                ->getAddress()
        ));

        if (!array_key_exists('Accept', $this->headers)) {
            $this->setHeader('Accept', 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8');
        }

        if (!array_key_exists('Accept-Language', $this->headers)) {
            $this->setHeader('Accept-Language', 'en-US;q=0.7,en;q=0.3');
        }

        if (!array_key_exists('User-Agent', $this->headers)) {
            $this->setHeader('User-Agent', 'phpGenerics 1.0');
        }

        if (!array_key_exists('Connection', $this->headers)) {
            if ($requestType == 'HEAD') {
                $this->setHeader('Connection', 'close');
            } else {
                $this->setHeader('Connection', 'keep-alive');
            }
        }

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

        $this->payload->reset();

        while ($this->payload->ready()) {
            $ms->write($this->payload->read(1024));
        }

        $ms->reset();

        if (! $this->isConnected()) {
            $this->connect();
        }

        while ($ms->ready()) {
            $this->write($ms->read(1024));
        }

        $this->payload = new MemoryStream();
        $this->headers = array();

        $delimiterFound = false;

        $tmp = "";
        $numBytes = 1;
        $start = time();
        while (true) {
            if (! $this->ready()) {
                if (time() - $start > $this->timeout) {
                    $this->disconnect();
                    throw new HttpException("Connection timed out!");
                }

                continue;
            }

            $c = $this->read($numBytes);
            if ($c === null) {
                break;
            }
            $start = time(); // we have readen something => adjust timeout start point
            $tmp .= $c;

            if (! $delimiterFound && substr($tmp, - 2, 2) == "\r\n") {
                if ("\r\n" == $tmp) {
                    $delimiterFound = true;

                    if ($requestType == 'HEAD') {
                        // Header readen, in type HEAD it is now time to leave
                        break;
                    }

                    if (isset($this->headers['Content-Length'])) {
                        // Try to read the whole payload at once
                        $numBytes = intval($this->headers['Content-Length']);
                    }
                } else {
                    if (strpos($tmp, ':') === false) {
                        $this->responseCode = HttpStatus::parseStatus($tmp)->getCode();
                    } else {
                        $tmp = trim($tmp);
                        list ($headerName, $headerValue) = explode(':', $tmp, 2);
                        $this->headers[$headerName] = $headerValue;
                    }
                }
                $tmp = "";
                continue;
            }

            if ($delimiterFound) {
                // delimiter already found, append to payload
                $this->payload->write($tmp);
                $tmp = "";

                if (isset($this->headers['Content-Length'])) {
                    if ($this->payload->count() >= $this->headers['Content-Length']) {
                        break;
                    }
                }
            }
        }

        if ($this->headers['Connection'] == 'close' && $this->isConnected()) {
            $this->disconnect();
        }

        // Set pointer to start
        $this->payload->reset();
    }

    /**
     * Retrieve the response status code
     *
     * @return int
     */
    public function getResponseCode()
    {
        return $this->responseCode;
    }
}
