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
 * Will be thrown whenever a php deprecated has occured
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class DeprecatedException extends ErrorException
{
}
