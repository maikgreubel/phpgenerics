<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams;

/**
 * Import dependencies
 */
require_once 'Generics/Streams/InputOutputStream.php';
require_once 'Generics/Streams/StreamException.php';

/**
 * This interface describes a socket stream for both input and output
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
interface SocketStream extends InputOutputStream
{
}