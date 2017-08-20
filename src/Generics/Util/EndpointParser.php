<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Util;

use Generics\Socket\Endpoint;
use Generics\Socket\InvalidUrlException;

/**
 * This class provides a parser to retrieve Endpoint objects out of arbitrary URIs
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class EndpointParser
{

    /**
     * Parse a URI into a Endpoint
     *
     * @param string $url
     * @throws InvalidUrlException
     * @return \Generics\Socket\Endpoint
     */
    public static function parseUrl($url): Endpoint
    {
        $url = UrlParser::parseUrl($url);
        
        return new Endpoint($url->getAddress(), $url->getPort());
    }
}
