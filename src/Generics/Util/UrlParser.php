<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Util;

use Generics\Socket\Endpoint;
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
    public static function parseUrl($url)
    {
        $parts = parse_url($url);

        if (! isset($parts['host']) || strlen($parts['host']) == 0) {
            throw new InvalidUrlException('This URL does not contain a host part');
        }
        if (! isset($parts['scheme']) || strlen($parts['scheme']) == 0) {
            throw new InvalidUrlException('This URL does not contain a scheme part');
        }

        $address = $parts['host'];
        $scheme = $parts['scheme'];
        $port = 0;
        $path = "/";

        if (isset($parts['port'])) {
            $port = intval($parts['port']);
        }

        if ($port == 0) {
            switch ($scheme) {
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
                    throw new InvalidUrlException("Scheme {scheme} is not handled!", array(
                        'scheme' => $scheme
                    ));
            }
        }

        if (isset($parts['path'])) {
            $path = $parts['path'];
        }

        return new Url($address, $port, $path, $scheme);
    }
}
