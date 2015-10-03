<?php
namespace Generics\Logger;

use Psr\Log\LogLevel;

/**
 * This class is a standard reference implementation of the PSR LoggerInterface.
 *
 * It logs everything to console. Depending on level it is written to stdout or stderr.
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class ConsoleLogger extends BasicLogger
{
    protected function logImpl($level, $message, array $context = array())
    {
        $channel = STDOUT;

        if ($level === LogLevel::ALERT || $level === LogLevel::CRITICAL || $level === LogLevel::EMERGENCY ||
            $level === LogLevel::ERROR || $level === LogLevel::WARNING) {
            $channel = STDERR;
        }

        fwrite($channel, $this->getMessage($level, $message, $context)->read(4096));
    }
}
