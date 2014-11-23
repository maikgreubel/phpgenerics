<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Util;

/**
 * This class provides some array utility functions
 *
 * @author Maik Greubel <greubel@nkey.de>
 *
 */
class Arrays
{
    /**
     * Create an empty array containing a specific number of elements
     *
     * @param int $numElements The number of elements in Array
     * @return \ArrayObject
     */
    public static function createEmptyArray($numElements)
    {
        return new \ArrayObject(array_fill(0, $numElements, null));
    }
}
