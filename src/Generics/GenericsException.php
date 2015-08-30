<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics;

use Generics\Util\Interpolator;

/**
 * This class provides a generic exception
 *
 * @author Maik
 */
class GenericsException extends \Exception
{
    use Interpolator;

    /**
     * Create a new GenericsException
     *
     * @param string $message
     *            The message to throw; May contain placeholder like {placeholder} and will be replaced by context
     *            elements
     * @param array $context
     *            The context elements to replace in message
     * @param number $code
     *            Optional code
     * @param \Exception $previous
     *            Optional previous exception
     */
    public function __construct($message, array $context = array(), $code = 0, \Exception $previous = null)
    {
        parent::__construct($this->interpolate($message, $context), $code, $previous);
    }
}
