<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Logger;

/**
 * This interface describes an implemention for a
 * logger which is capable to log variable dumps.
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
interface DumpLogger
{

    /**
     * Dumps an arbitrary variable into log.
     *
     * @param mixed $o            
     */
    public function dump($o);
}
