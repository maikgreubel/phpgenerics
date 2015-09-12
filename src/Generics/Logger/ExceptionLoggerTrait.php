<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Logger;

use Psr\Log\LogLevel;

/**
 * Implementation for logging exceptions
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
trait ExceptionLoggerTrait
{
    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Logger\ExceptionLogger::logException()
     */
    public function logException(\Exception $ex)
    {
        $level = LogLevel::ALERT;

        if ($ex instanceof \ErrorException) {
            $level = LogLevel::ERROR;
        } elseif ($ex instanceof \RuntimeException) {
            $level = LogLevel::EMERGENCY;
        }

       $this->logImpl($level, "({code}): {message}\n{stackTrace}", array(
            'code' => $ex->getCode(),
            'message' => $ex->getMessage(),
            'stackTrace' => $ex->getTraceAsString()
        ));
    }
}
