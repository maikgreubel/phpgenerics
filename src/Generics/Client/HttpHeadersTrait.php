<?php
/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Client;

use Generics\Util\Arrays;

/**
 * This trait provides common http(s) header functionality
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
trait HttpHeadersTrait
{

    /**
     * Headers
     *
     * @var array
     */
    private $headers;

    /**
     * The response status code
     *
     * @var int
     */
    private $responseCode;
    
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
     */
    public function resetHeaders()
    {
        $this->headers = array();
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
     * Retrieve the response status code
     *
     * @return int
     */
    public function getResponseCode(): int
    {
        return $this->responseCode;
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
            if (function_exists('gzinflate')) {
                $encoding = 'gzip, deflate';
            } else {
                $encoding = 'identity';
            }
            $this->setHeader('Accept-Encoding', $encoding);
        }
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
     * Retrieve content type from headers
     * 
     * @return string
     */
    private function getContentEncoding(): string
    {
        return $this->getHeader('Content-Encoding');
    }

    /**
     * Retrieve an given header
     *
     * @param string $name
     * @return string
     */
    private function getHeader(string $name): string
    {
        $result = "";
        
        if (Arrays::hasElement($this->headers, $name)) {
            $result = $this->headers[$name];
        }
        
        return $result;
    }
}
