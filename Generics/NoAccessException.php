<?php

/**
 * This file is part of the PHP Generics package.
 * 
 * @package Generics
 */
namespace Generics;

/**
 * Import dependencies
 */
require_once 'Generics/GenericsException.php';

/**
 * Derived exception
 *
 * Will be thrown whenever the access to a resource is prohibited.
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class NoAccessException extends GenericsException
{
}