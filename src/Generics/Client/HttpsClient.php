<?php
/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Client;

use Generics\Socket\Url;
use Generics\Socket\SecureClientSocket;
use Generics\Streams\HttpStream;

/**
 * This class provides a https client connection
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class HttpsClient extends SecureClientSocket implements HttpStream
{
    use HttpClientTrait;
    
    /**
     * Create a new https client
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
        
        $this->setTimeout($timeout);
        $this->setPath($url->getPath());
        $this->setProtocol($proto);
        $this->setQueryString($url->getQueryString());
        $this->reset();
        $this->resetHeaders();
    }
    
    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\HttpStream::request()
     */
    public function request(string $requestType)
    {
        $this->requestImpl($requestType);
    }
}