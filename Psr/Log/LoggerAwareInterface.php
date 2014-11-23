<?php

/**
 * This file is part of the PSR (PHP Standard Recommendation) package.
 *
 * @package psr/log
 */
namespace Psr\Log;

/**
 * Describes a logger-aware instance
 */
interface LoggerAwareInterface
{

    /**
     * Sets a logger instance on the object
     *
     * @param LoggerInterface $logger            
     * @return null
     */
    public function setLogger(LoggerInterface $logger);
}