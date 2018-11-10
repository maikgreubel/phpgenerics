<?php
namespace Generics\Tests;

use Generics\Client\Session;
use PHPUnit\Framework\TestCase;
use ArgumentCountError;
use TypeError;

class SessionsTest extends TestCase
{
    public function testSet()
    {
        $tempArray = [];
        $session = new Session($tempArray);

        $session->put('fump', 'doll');

        $this->assertArrayHasKey('fump', $tempArray);
        $this->assertEquals('doll', $tempArray['fump']);
    }

    public function testNoSessionProvided()
    {
        $phpVersion = substr(phpversion(), 0, 3);

        if ($phpVersion === '7.1' || $phpVersion === '7.2') {
            $this->expectException(ArgumentCountError::class);
        } elseif ($phpVersion === '7.0') {
            $this->expectException(TypeError::class);
        }
        $session = new Session();
    }
}
