<?php
namespace Generics\Tests;

use Generics\Client\Session;
use PHPUnit\Framework\TestCase;
use ArgumentCountError;
use TypeError;

class SessionsTest extends TestCase
{

    /**
     * @test
     */
    public function testSet()
    {
        $tempArray = [];
        $session = new Session($tempArray);
        
        $session->put('fump', 'doll');
        
        $this->assertArrayHasKey('fump', $tempArray);
        $this->assertEquals('doll', $tempArray['fump']);
    }

    /**
     * @test
     */
    public function testNoSessionProvided()
    {
        if(substr(phpversion(), 0, 3) === '7.1') {
            $this->expectException(ArgumentCountError::class);
        }
        else if(substr(phpversion(), 0, 3) === '7.0') {
            $this->expectException(TypeError::class);
        }
        $session = new Session();
    }
}