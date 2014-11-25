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
     */
    public function setHeader($headerName, $headerValue)
    {
        $this->headers[$headerName] = $headerValue;
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
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\HttpStream::request()
     */
    public function request($requestType)
    {
        if ($this->secure) {
            throw new HttpException("Secure connection using HTTPs is not supported yet!");
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

        // Add all existing headers
        foreach ($this->headers as $headerName => $headerValue) {
            $ms->interpolate("{headerName}: {headerValue}\r\n", array(
                'headerName' => $headerName,
                'headerValue' => $headerValue
            ));
        }

        $ms->write("\r\n");
        $this->payload->reset();

        while ($this->payload->ready()) {
            $ms->write($this->payload->read(1024));
        }

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

            $tmp .= $c;

            if (! $delimiterFound && substr($tmp, - 2, 2) == "\r\n") {
                if ("\r\n" == $tmp) {
                    $delimiterFound = true;

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
