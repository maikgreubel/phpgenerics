<?php
/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams\Interceptor;

/**
 * This abstract class provides a basic stream interceptor
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
abstract class AbstractStreamInterceptor implements StreamInterceptor
{

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\Interceptor\StreamInterceptor::onClose()
     */
    public function onClose()
    {
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\Interceptor\StreamInterceptor::onCreate()
     */
    public function onCreate()
    {
    }
}
