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
     * @param string $url
     * @throws InvalidUrlException
     * @return \Generics\Socket\Endpoint
     */
    public static function parseUrl($url)
    {
        $parts = parse_url($url);

        if(!isset($parts['scheme']))
            throw new InvalidUrlException('This URL does not contain a scheme part');
        if(!isset($parts['host']))
            throw new InvalidUrlException('This URL does not contain a host part');

        $address = $parts['host'];
        $port = 0;
        if(isset($parts['port']))
            $port = intval($parts['port']);

        if($port == 0)
        {
            switch($parts['scheme'])
            {
                case 'http':
                    $port = 80;
                    break;

                case 'https':
                    $port = 443;
                    break;

                case 'ftp':
                    $port = 21;
                    break;

                default:
                    throw new InvalidUrlException("Scheme {scheme} is not handled!", array('scheme' => $parts['scheme']));
            }
        }

        return new Endpoint($address, $port);
    }
}