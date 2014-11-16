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
require_once 'Generics/Streams/InputStream.php';
require_once 'Generics/Streams/OutputStream.php';

/**
 * This interface describes the implementation of an input-output-stream by combining the 
 * InputStream and OutputStream interfaces.
 * 
 * @author Maik Greubel <greubel@nkey.de>
 */
interface InputOutputStream extends InputStream, OutputStream
{
}