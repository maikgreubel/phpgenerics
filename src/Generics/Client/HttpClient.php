<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Client;

use Generics\Socket\ClientSocket;
use Generics\Socket\Url;
use Generics\Streams\HttpStream;

/**
 * This class implements a HttpStream as client
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class HttpClient extends ClientSocket implements HttpStream
{
    use HttpClientTrait;
    
    /**
     * Whether to use https instead of http
     *
     * @var boolean
     */
    private $secure;

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
        
        $this->secure = $url->getScheme() == 'https';

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
        if ($this->secure) {
            throw new HttpException("Secure connection using HTTPs is not supported!");
        }
        
        $this->requestImpl($requestType);
    }
}
