<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * This trait provides common logger functionalitys
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
trait LoggerTrait
{

    /**
     * Logger instance
     *
     * @var \Psr\Log\LoggerInterface
     */
    private $logger = null;

    /**
     *
     * {@inheritdoc}
     * @see \Psr\Log\LoggerAwareInterface::setLogger()
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Initialize the logger instance
     */
    private function initLogger()
    {
        if (null === $this->logger) {
            $this->logger = new NullLogger();
        }
    }

    /**
     * Retrieve the logger instance
     *
     * @return \Psr\Log\LoggerInterface
     */
    public function getLog()
    {
        $this->initLogger();
        
        return $this->logger;
    }
}
