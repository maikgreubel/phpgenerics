<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Logger;

use Generics\Streams\FileOutputStream;

/**
 * This class is a standard reference implementation of the PSR LoggerInterface.
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class SimpleLogger extends BasicLogger
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
     * @param integer $level
     *            The arbitrary level
     * @param string $message
     *            The message to log
     * @param array $context
     *            The context of logging
     */
    protected function logImpl($level, $message, array $context = array())
    {
    	if (!$this->levelHasReached($level)) {
    		return;
    	}
    	
        if ($this->isRotationNeeded()) {
            unlink($this->file);
        }

        $ms = $this->getMessage($level, $message, $context);

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
