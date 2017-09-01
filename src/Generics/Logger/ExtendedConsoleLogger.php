<?php
/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Logger;

/**
 * This class is an extended implementation of PSR logger
 *
 * It provides additional functionality to log objects and exceptions to console.
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class ExtendedConsoleLogger extends ConsoleLogger implements ExceptionLogger, DumpLogger
{
    use DumpLoggerTrait, ExceptionLoggerTrait;
}
