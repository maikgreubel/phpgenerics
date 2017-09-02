<?php
namespace Generics\Tests;

use PHPUnit\Framework\TestCase;
use Generics\Streams\Interceptor\CachedStreamInterceptor;
use Generics\Streams\StandardOutputStream;

class StandardOutputStreamTest extends TestCase
{
    /**
     * @test
     */
    public function testOutputUsingInterceptor()
    {
        $interceptor = new CachedStreamInterceptor();
        $this->assertEmpty($interceptor->getCache());
        
        $stdout = new StandardOutputStream();
        $stdout->setInterceptor($interceptor);
        
        $this->assertTrue($stdout->isOpen());
        $this->assertTrue($stdout->isWriteable());
        $this->assertTrue($stdout->ready());
        
        $stdout->write("very important content");
        $this->assertEquals("very important content", $interceptor->getCache());
        $this->assertEquals(0, $stdout->count());
        
        $stdout->flush();
        
        $this->assertEmpty($interceptor->getCache());
        
        $stdout->close();
        
        $this->assertFalse($stdout->isOpen());
        $this->assertFalse($stdout->isWriteable());
        $this->assertFalse($stdout->ready());
        
        $stdout->reset();

        $this->assertTrue($stdout->isOpen());
        $this->assertTrue($stdout->isWriteable());
        $this->assertTrue($stdout->ready());
        $this->assertEmpty($interceptor->getCache());
        
        $stdout->write("second data");
        $this->assertEquals("second data", $interceptor->getCache());
    }
}