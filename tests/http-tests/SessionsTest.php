<?php
namespace Generics\Tests;

use Generics\Client\Session;
use PHPUnit\Framework\TestCase;
use ArgumentCountError;

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
        $this->expectException(ArgumentCountError::class);
        $session = new Session();
    }
}