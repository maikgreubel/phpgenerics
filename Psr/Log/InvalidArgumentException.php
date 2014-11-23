<?php

/**
 * This file is part of the PSR (PHP Standard Recommendation) package.
 * 
 * @package psr/log
 */
namespace Psr\Log;

/**
 * This class derives from SPL's InvalidArgumentException
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class InvalidArgumentException extends \InvalidArgumentException
{

    /**
     * Create a new InvalidArgumentException instance
     *
     * @param unknown $message            
     * @param number $code            
     * @param string $prev            
     */
    public function __construct($message, $code = 0, $prev = null)
    {
        parent::__construct($message, $code, $prev);
    }
}