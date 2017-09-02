<?php
/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams\Interceptor;

/**
 * This class provides an caching stream interceptor
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class CachedStreamInterceptor extends AbstractStreamInterceptor
{

    /**
     *
     * @var string
     */
    private static $cache = "";

    /**
     * Create a new instance of CachedStreamInterceptor
     */
    public function __construct()
    {
        stream_filter_register($this->getFilterName(), CachedStreamInterceptor::class);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\Interceptor\StreamInterceptor::filter()
     */
    public function filter($in, $out, int &$consumed, bool $closing): int
    {
        if ($closing) {
            return PSFS_FEED_ME;
        }
        
        while ($bucket = stream_bucket_make_writeable($in)) {
            self::$cache .= $bucket->data;
            $consumed += $bucket->datalen;
            stream_bucket_append($out, $bucket);
        }
        return PSFS_PASS_ON;
    }

    /**
     * Retrieve the cache buffer
     * 
     * @return string
     */
    public function getCache(): string
    {
        return self::$cache;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\Interceptor\StreamInterceptor::getFilterName()
     */
    public function getFilterName(): string
    {
        return strtolower(CachedStreamInterceptor::class);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\Interceptor\StreamInterceptor::reset()
     */
    public function reset()
    {
        self::$cache = "";
    }
}