<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams;

/**
 * This interface describes the implementation of an input-output-stream by combining the
 * InputStream and OutputStream interfaces.
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
interface InputOutputStream extends InputStream, OutputStream
{
}
