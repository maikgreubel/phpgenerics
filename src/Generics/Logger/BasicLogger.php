<?php
namespace Generics\Logger;

use Generics\Streams\MemoryStream;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

abstract class BasicLogger extends AbstractLogger
{
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
     * Format the message to log and return a memory stream of it
     *
     * @param integer $level
     *            The arbitrary level
     * @param string $message
     *            The message to log
     * @param array $context
     *            The context of logging
     *
     * @return \Generics\Streams\MemoryStream The formatted message
     */
    protected function getMessage($level, $message, array $context = array())
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

        return $ms;
    }

    /**
     * Must be implemented by concrete logger
     *
     * @param integer $level
     *            The arbitrary level
     * @param string $message
     *            The message to log
     * @param array $context
     *            The context of logging
     */
    abstract protected function logImpl($level, $message, array $context = array());

    /**
     * {@inheritDoc}
     * @see \Psr\Log\LoggerInterface::log()
     */
    public function log($level, $message, array $context = array())
    {
        $this->logImpl($level, $message, $context);
    }
}
