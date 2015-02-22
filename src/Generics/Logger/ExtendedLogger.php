<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Logger;

use Generics\Logger\DumpLogger;
use Generics\Logger\DumpLoggerTrait;
use Generics\Logger\ExceptionLogger;
use Generics\Logger\ExceptionLoggerTrait;
use Generics\Logger\SimpleLogger;

/**
 * This class is an extended implementation of PSR logger
 *
 * It provides additional functionality to log objects and exceptions.
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class ExtendedLogger extends SimpleLogger implements ExceptionLogger, DumpLogger
{
    use DumpLoggerTrait, ExceptionLoggerTrait;
}
