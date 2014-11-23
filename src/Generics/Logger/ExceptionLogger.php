<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Logger;

/**
 * This interface describes an implemention for a
 * logger which is capable to log exceptions.
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
interface ExceptionLogger
{

    /**
     * Log an exception
     *
     * The exception will be logged according its severity.
     * ErrorException will be logged as LogLevel::Error
     * RuntimeExcpetion will be logged as LogLevel::
     *
     * @param Exception $ex
     *            The exception to log.
     */
    public function logException(\Exception $ex);
}
