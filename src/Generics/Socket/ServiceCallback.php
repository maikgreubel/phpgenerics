<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Socket;

/**
 * This abstract class is the blueprint for implementing a service callback
 *
 * @author Maik <greubel@nkey.de>
 *
 */
abstract class ServiceCallback
{

    /**
     * Endpoint
     *
     * @var Service endpoint
     */
    private $serverEndpoint;

    /**
     * Create a new service callback instance
     *
     * @param Endpoint $endPoint
     *            The endpoint
     */
    public function __construct(Endpoint $endPoint)
    {
        $this->serviceEndpoint = $endPoint;
    }

    /**
     * Any implementor must provide such a functions
     *
     * @return boolean true in case of service should keep on running, false if it shall stop
     */
    abstract public function callback(Socket $socket);
}
