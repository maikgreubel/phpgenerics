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
 * It provides additional functionality to log objects and exceptions.
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class ExtendedLogger extends SimpleLogger implements ExceptionLogger, DumpLogger
{
    use DumpLoggerTrait, ExceptionLoggerTrait;
}
