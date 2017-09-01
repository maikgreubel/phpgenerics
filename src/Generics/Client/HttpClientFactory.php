<?php
/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Client;

use Generics\Socket\Url;
use Generics\Streams\HttpStream;

/**
 * This class provides a factory for http(s) clients
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
final class HttpClientFactory
{

    private function __construct()
    {
        // Nothing
    }

    /**
     * Create a new instance of Http(s) client for given url
     *
     * @param Url $url
     *            The url to create a http(s) client instance for
     *            
     * @return HttpStream
     */
    public static function get(Url $url): HttpStream
    {
        if ($url->getScheme() === 'https') {
            return new HttpsClient($url);
        }
        return new HttpClient($url);
    }
}