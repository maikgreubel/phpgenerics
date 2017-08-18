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
     * The query string
     */
    private $queryString;

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
    public function __construct($address, $port = 80, $path = '/', $scheme = 'http', $queryString = '')
    {
        try {
            $parsed = UrlParser::parseUrl($address);
            parent::__construct($parsed->getAddress(), $parsed->getPort());
            $this->path = $parsed->getPath();
            $this->scheme = $parsed->getScheme();
            $this->queryString = $parsed->getQueryString();
        } catch (InvalidUrlException $ex) {
            parent::__construct($address, $port);
            $this->path = $path;
            $this->scheme = $scheme;
            $this->queryString = $queryString;
        }
    }

    /**
     * Get the scheme of the url
     *
     * @return string
     */
    public function getScheme(): string
    {
        return $this->scheme;
    }

    /**
     * Get the path of the url
     *
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * Retrieve the url as string
     *
     * @return string
     */
    public function getUrlString(): string
    {
        $query = "";
        if (strlen($this->queryString) > 0) {
            $query = sprintf("?%s", $this->queryString);
        }
        
        if (($this->scheme == 'http' && $this->getPort() == 80) || ($this->scheme == 'ftp' && $this->getPort() == 21) || ($this->scheme == 'https' && $this->getPort() == 443)) {
            return sprintf("%s://%s%s%s", $this->scheme, $this->getAddress(), $this->path, $query);
        }
        return sprintf("%s://%s:%d%s%s", $this->scheme, $this->getAddress(), $this->getPort(), $this->path, $query);
    }

    /**
     * Retrieve only the file part
     *
     * @return string
     */
    public function getFile(): string
    {
        return basename($this->path);
    }

    public function getQueryString(): string
    {
        return $this->queryString;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Socket\Endpoint::__toString()
     */
    public function __toString(): string
    {
        return $this->getUrlString();
    }
}
