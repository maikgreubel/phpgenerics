<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Psr\Log\AbstractLogger;
use Generics\Streams\MemoryStream;
use Generics\Streams\FileOutputStream;

/**
 * This class is a standard reference implementation of the PSR LoggerInterface.
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class SimpleLogger extends AbstractLogger implements ExceptionLogger, DumpLogger
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
        self::checkLevel($level);

        $ms = new MemoryStream();

        $ms->write(strftime("%Y-%m-%d %H:%M:%S", time()));
        $ms->interpolate("\t[{level}]: ", array('level' => sprintf("%6.6s", $level)));
        $ms->interpolate($message, $context);
        $ms->write("\n");

        if ($this->isRotationNeeded()) {
            unlink($this->file);
        }

        $fos = new FileOutputStream($this->file, true);
        $fos->write($ms);
        $fos->flush();
        $fos->close();
    }

    /**
     * Checks whether a rotation of log file is necessary
     *
     * @return boolean true in case of its necessary, false otherwise
     */
    private function isRotationNeeded()
    {
        clearstatcache();

        if (! file_exists($this->file)) {
            return false;
        }

        $result = false;

        $attributes = stat($this->file);

        if ($attributes == false || $attributes['size'] >= $this->maxLogSize * 1024 * 1024) {
            $result = true;
        }

        return $result;
    }

    /**
     * Checks the given level
     *
     * @param string $level
     * @throws \Psr\Log\InvalidArgumentException
     */
    private static function checkLevel($level)
    {
        if ($level != LogLevel::ALERT && $level != LogLevel::CRITICAL && $level != LogLevel::DEBUG && //
            $level != LogLevel::EMERGENCY && $level != LogLevel::ERROR && $level != LogLevel::INFO && //
            $level != LogLevel::NOTICE && $level != LogLevel::WARNING) {
            throw new \Psr\Log\InvalidArgumentException("Invalid log level provided!");
        }
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

    /**
     * Retrieve the maximum size of log file in megabytes
     *
     * @return int
     */
    public function getMaxLogSize()
    {
        return $this->maxLogSize;
    }
}
