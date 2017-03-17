<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics;

/**
 * This interface describes a lockable implementation
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
interface Lockable
{

    /**
     * Lock resource.
     *
     * @throws \Generics\LockException in case of lock has failed
     */
    public function lock();

    /**
     * Unlock resource
     *
     * @throws \Generics\LockException in case of unlock has failed
     */
    public function unlock();
    
    /**
     * Return the current locking state
     * 
     * @return true in case of lock is enabled, false otherwise
     */
    public function isLocked();
}
