<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Socket;

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
     * @param string $address   The address for the url
     * @param int $port         The port for the url
     * @param string $scheme    The scheme for the url
     * @param string $path      The path for the url
     */
    public function __construct($address, $port, $scheme, $path)
    {
        parent::__construct($address, $port);
        $this->path = $path;
        $this->scheme = $scheme;
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

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Socket\Endpoint::__toString()
     */
    public function __toString()
    {
        return sprintf("%s://%s:%d/%s", $this->scheme, $this->getAddress(), $this->getPort(), $this->path);
    }
}
