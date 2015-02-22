<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Logger;

/**
 * Implementation for logging dumps of objects
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
trait DumpLoggerTrait
{
    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Logger\DumpLogger::dump()
     */
    public function dump($o)
    {
        $out = var_export($o, true);
        $this->debug("Contents of {object}\n{dump}", array(
            'object' => gettype($o),
            'dump' => $out
        ));
    }
}
