<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * This class is a standard reference implementation of the PSR LoggerInterface.
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class SimpleLogger implements LoggerInterface, ExceptionLogger, DumpLogger
{

    /**
     * The log file path
     *
     * @var string
     */
    private $file = null;

    /**
     * The maximum log file size in megabyte
     *
     * @var int
     */
    private $maxLogSize;

    /**
     * Create a new SimpleLogger instance
     *
     * @param string $logFilePath
     *            The path to the log file.
     * @param int $maxLogSize
     *            The maximum log file size before it is rotated.
     */
    public function __construct($logFilePath = 'application.log', $maxLogSize = 2)
    {
        $this->file = $logFilePath;
        $this->maxLogSize = intval($maxLogSize);

        if ($this->maxLogSize < 1 || $this->maxLogSize > 50) {
            $this->maxLogSize = 2;
        }
    }

    /**
     * Interpolates context values into the message placeholders.
     * Taken as copy & paste from PSR document.
     */
    private function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            $replace['{' . $key . '}'] = $val;
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }

    /**
     * This function provides the real logging function.
     *
     * First the log file size is checked.
     * When the maximum size has reached, the file will be overwritten.
     * Otherwise the log string is appended.
     *
     * @param mixed $level
     *            The arbitrary level
     * @param string $message
     *            The message to log
     * @param array $context
     *            The context of logging
     */
    private function logImpl($level, $message, array $context = array())
    {
        /**
         * This check implements the specification request.
         */
        if ($level != LogLevel::ALERT && $level != LogLevel::CRITICAL && $level != LogLevel::DEBUG && //
$level != LogLevel::EMERGENCY && $level != LogLevel::ERROR && $level != LogLevel::INFO && //
$level != LogLevel::NOTICE && $level != LogLevel::WARNING) {
            throw new \Psr\Log\InvalidArgumentException("Invalid log level provided!");
        }

        clearstatcache();

        $mode = "a";
        if (! file_exists($this->file)) {
            $mode = "w";
        } else {
            $attributes = stat($this->file);
            if ($attributes == false || $attributes['size'] >= $this->maxLogSize * 1024 * 1024) {
                $mode = "w";
            }
        }

        $fd = fopen($this->file, $mode);
        if ($fd) {
            if (count($context) > 0) {
                $message = $this->interpolate($message, $context);
            }

            $time = strftime("%Y-%m-%d %H:%M:%S", time());
            fprintf($fd, "%s\t[%6.6s]: %s\n", $time, $level, $message);
            fflush($fd);
            fclose($fd);
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Psr\Log\LoggerInterface::emergency()
     */
    public function emergency($message, array $context = array())
    {
        $this->logImpl(LogLevel::EMERGENCY, $message, $context);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Psr\Log\LoggerInterface::alert()
     */
    public function alert($message, array $context = array())
    {
        $this->logImpl(LogLevel::ALERT, $message, $context);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Psr\Log\LoggerInterface::critical()
     */
    public function critical($message, array $context = array())
    {
        $this->logImpl(LogLevel::CRITICAL, $message, $context);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Psr\Log\LoggerInterface::error()
     */
    public function error($message, array $context = array())
    {
        $this->logImpl(LogLevel::ERROR, $message, $context);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Psr\Log\LoggerInterface::warning()
     */
    public function warning($message, array $context = array())
    {
        $this->logImpl(LogLevel::WARNING, $message, $context);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Psr\Log\LoggerInterface::notice()
     */
    public function notice($message, array $context = array())
    {
        $this->logImpl(LogLevel::NOTICE, $message, $context);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Psr\Log\LoggerInterface::info()
     */
    public function info($message, array $context = array())
    {
        $this->logImpl(LogLevel::INFO, $message, $context);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Psr\Log\LoggerInterface::debug()
     */
    public function debug($message, array $context = array())
    {
        $this->logImpl(LogLevel::DEBUG, $message, $context);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Psr\Log\LoggerInterface::log()
     */
    public function log($level, $message, array $context = array())
    {
        $this->logImpl($level, $message, $context);
    }

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

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Logger\DumpLogger::dump()
     */
    public function dump($o)
    {
        $out = var_export($o, true);
        $this->debug("Contents of {object}\n{dump}", array(
            'object' => gettype($o),
            'dump' => $out
        ));
    }

    /**
     * Retrieve the file name of logger
     *
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }
}
