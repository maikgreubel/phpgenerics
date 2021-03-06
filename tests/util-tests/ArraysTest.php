<?php

namespace Generics\Tests;

use Generics\Util\Arrays;

class ArraysTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateEmptyArray()
    {
        $array = Arrays::createEmptyArray(10);

        $this->assertEquals(10, $array->count());
    }
}
