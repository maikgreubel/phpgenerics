<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics;

/**
 * This class provides a generic exception
 *
 * @author Maik
 */
class GenericsException extends \Exception
{

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
     * @param string $previous
     *            Optional previous exception
     */
    public function __construct($message, array $context = array(), $code = 0, $previous = null)
    {
        parent::__construct($this->interpolate($message, $context), $code, $previous);
    }

    /**
     * Interpolates context values into the message placeholders.
     *
     * @param string $message
     *            The message containing placeholders
     * @param array $context
     *            The context array containing the replacers
     *
     * @return string The interpolated message
     */
    private function interpolate($message, array $context = array())
    {
        $replace = array();

        if ($context !== null) {
            foreach ($context as $key => $val) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        return strtr($message, $replace);
    }
}