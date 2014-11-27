<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Socket;

use Generics\Util\UrlParser;

/**
 * This class provides a data holder for a url
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class Url extends Endpoint
{
    /**
     * Scheme part of url
     *
     * @var string
     */
    private $scheme;

    /**
     * Path part of url
     *
     * @var string
     */
    private $path;

    /**
     * Create a new Url instance
     *
     * @param string $address
     *            The address for the url (either only address or full url)
     * @param int $port
     *            The port for the url
     * @param string $path
     *            The path for the url
     * @param string $scheme
     *            The scheme for the url
     */
    public function __construct($address, $port = 80, $path = '/', $scheme = 'http')
    {
        try {
            $parsed = UrlParser::parseUrl($address);
            parent::__construct($parsed->getAddress(), $parsed->getPort());
            $this->path = $parsed->getPath();
            $this->scheme = $parsed->getScheme();
        } catch (InvalidUrlException $ex) {
            parent::__construct($address, $port);
            $this->path = $path;
            $this->scheme = $scheme;
        }
    }

    /**
     * Get the scheme of the url
     *
     * @return string
     */
    public function getScheme()
    {
        return $this->scheme;
    }

    /**
     * Get the path of the url
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    public function getUrlString()
    {
        if (($this->scheme == 'http' && $this->getPort() == 80) ||
            ($this->scheme == 'ftp' && $this->getPort() == 21) ||
            ($this->scheme == 'https' && $this->getPort() == 443))
        {
            return sprintf("%s://%s%s", $this->scheme, $this->getAddress(), $this->path);
        }
        return sprintf("%s://%s:%d%s", $this->scheme, $this->getAddress(), $this->getPort(), $this->path);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Socket\Endpoint::__toString()
     */
    public function __toString()
    {
        return $this->getUrlString();

    }
}
