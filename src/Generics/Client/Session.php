<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Client;

/**
 * This class provides session provider
 *
 * @author Maik Greubel <greubel@nkey.de>
 *
 */
class Session
{

    private $sessionId;

    /**
     * Session variable container
     * @var array
     */
    private $sessionContainer;

    /**
     * Create a new session provider
     */
    public function __construct(array &$container)
    {
        $this->sessionContainer = &$container;
    }

    /**
     * Create session
     */
    public function create()
    {
        session_start();
        $this->sessionId = session_id();
    }

    /**
     * Destroy session
     */
    public function destroy()
    {
        session_destroy();
    }

    /**
     * Add a new identifier with corresponding value to session storage
     *
     * @param string $key
     * @param string $value
     */
    public function put($key, $value)
    {
        $this->sessionContainer[$key] = $value;
    }

    /**
     * Retrieve value from session storage for corresponding key
     *
     * @param string $key
     * @return NULL|string
     */
    public function get($key)
    {
        if (! isset($this->sessionContainer[$key])) {
            return null;
        }

        return $this->sessionContainer[$key];
    }
}
