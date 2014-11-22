<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Client;

/**
 * Import dependencies
 */
require_once 'Generics/GenericsException.php';

use Generics\GenericsException;

/**
 * Derived exception used for HTTP exceptions
 * 
 * @author Maik Greubel <greubel@nkey.de>
 */
class HttpException extends GenericsException
{
  
}