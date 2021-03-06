<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Util;

use Generics\Socket\InvalidUrlException;
use Generics\Socket\Url;

/**
 * This class provides a parser to retrieve Url objects out of arbitrary URIs
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class UrlParser
{

    /**
     * Parse a URI into a Url
     *
     * @param string $url
     * @throws InvalidUrlException
     * @return \Generics\Socket\Url
     */
    public static function parseUrl($url): Url
    {
        $parts = parse_url($url);
        
        if (false === $parts || false === Arrays::hasElement($parts, 'host') || false === Arrays::hasElement($parts, 'scheme')) {
            throw new InvalidUrlException('The URL {url} does not contain necessary parts', array('url' => $url));
        }
        
        $address = $parts['host'];
        $scheme = $parts['scheme'];
        $query = (isset($parts['query']) ? $parts['query'] : '');
        $port = 0;
        $path = "/";
        
        if (isset($parts['port'])) {
            $port = intval($parts['port']);
        }
        
        if ($port == 0) {
            $port = self::getPortByScheme($scheme);
        }
        
        if (isset($parts['path'])) {
            $path = $parts['path'];
        }
        
        return new Url($address, $port, $path, $scheme, $query);
    }

    /**
     * Get port number by scheme name.
     * The port will be the default which is defined by
     * http://en.wikipedia.org/wiki/List_of_TCP_and_UDP_port_numbers
     *
     * @param string $scheme
     *            The scheme.
     * @throws InvalidUrlException
     * @return int
     */
    public static function getPortByScheme($scheme): int
    {
        switch ($scheme) {
            case 'http':
                return 80;
            
            case 'https':
                return 443;
            
            case 'ftp':
                return 21;
            
            default:
                throw new InvalidUrlException("Scheme {scheme} is not handled!", array(
                    'scheme' => $scheme
                ));
        }
    }
}
