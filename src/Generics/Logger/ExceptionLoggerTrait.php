<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Logger;

use Psr\Log\LogLevel;
use ErrorException;
use Exception;
use RuntimeException;

/**
 * Implementation for logging exceptions
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
trait ExceptionLoggerTrait
{
	abstract protected function logImpl($level, $message, array $context = array());

    /**
     * {@inheritDoc}
     * @see \Generics\Logger\ExceptionLogger::logException()
     */
    public function logException(Exception $ex)
    {
        $level = LogLevel::ALERT;

        if ($ex instanceof ErrorException) {
            $level = LogLevel::ERROR;
        } elseif ($ex instanceof RuntimeException) {
            $level = LogLevel::EMERGENCY;
        }

        $this->logImpl($level, "({code}): {message}\n{stackTrace}", array(
            'code' => $ex->getCode(),
            'message' => $ex->getMessage(),
            'stackTrace' => $ex->getTraceAsString()
        ));
    }
}
