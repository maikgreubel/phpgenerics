<?php
/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams\Interceptor;

/**
 * This interface describes a stream interceptor
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
interface StreamInterceptor
{

    /**
     * Applies the filter
     *
     * @param resource $in
     * @param resource $out
     * @param int $consumed
     * @param bool $closing
     * @return int
     */
    public function filter($in, $out, int &$consumed, bool $closing): int;

    /**
     * Called when closing the filter
     */
    public function onClose();

    /**
     * Called when creating the filter
     */
    public function onCreate();

    /**
     * Retrieve filter name
     *
     * @return string
     */
    public function getFilterName(): string;

    /**
     * Reset filter
     */
    public function reset();
}