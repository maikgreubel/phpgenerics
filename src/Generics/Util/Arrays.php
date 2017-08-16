<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Util;

use ArrayObject;

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
     * @return array
     */
    public static function createEmptyArray($numElements):ArrayObject
    {
        return new ArrayObject(array_fill(0, $numElements, null));
    }
}
