<?php
/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics;

use ErrorException;

/**
 * Derived exception
 *
 * Will be thrown whenever a php recoverable error has occured
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class RecoverableErrorException extends ErrorException
{
}
