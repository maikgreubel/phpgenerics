<?php

namespace Generics\Tests;

use Generics\Streams\MemoryStream;
use Generics\Streams\FileInputStream;

class MemoryStreamTest extends \PHPUnit\Framework\TestCase
{

    private $testFile = 'memory.dat';

    private $testData = "This data will only appear in memory. Writing to persistent files is not possible!";

    public function setUp()
    {
        file_put_contents($this->testFile, $this->testData);
    }

    public function tearDown()
    {
        if (file_exists($this->testFile)) {
            unlink($this->testFile);
        }
    }

    public function testMemoryWrite()
    {
        $ms = new MemoryStream();
        $this->assertTrue($ms->isWriteable());
        $ms->write($this->testData);

        $this->assertEquals(strlen($this->testData), $ms->count());
    }

    /**
     * @expectedException \Generics\Streams\StreamException
     */
    public function testReadAfterClose()
    {
        $ms = new MemoryStream();
        $ms->close();

        $ms->write($this->testData);
    }

    public function testWriteAndRead()
    {
        $ms = new MemoryStream();
        $ms->write($this->testData);

        $out = $ms->read(1024);

        $this->assertEquals($this->testData, $out);

        $ms->write($this->testData);

        $out = $ms->read(1024);
        $this->assertEquals($this->testData, $out);

        $out = $ms->read(1024);
        $this->assertEmpty($out);

        $ms->reset();
        $out = $ms->read(1024);

        $this->assertEquals("{$this->testData}{$this->testData}", $out);

        $this->assertEquals(strlen($this->testData) * 2, $ms->count());
    }

    public function testFlush()
    {
        $ms = new MemoryStream();
        $ms->write($this->testData);
        $ms->flush();

        $this->assertEquals(0, $ms->count());
    }

    public function testMemoryFromInput()
    {
        $fis = new FileInputStream($this->testFile);

        $in = $fis->read(1024);

        $ms = new MemoryStream($fis);

        $this->assertEquals(strlen($in), $ms->count());

        $in2 = $ms->read(1024);

        $this->assertNotEmpty($in2);

        $this->assertEquals($in, $in2);
    }
    
    public function testSlurp()
    {
        $fis = new FileInputStream($this->testFile);
        
        $in = $fis->read(1024);
        
        $ms = new MemoryStream($fis);
        
        $str = $ms->slurp();
        
        $this->assertEquals($in, $str);
    }

    /**
     * @expectedException \Generics\Streams\StreamException
     */
    public function testReadClosed()
    {
        $ms = new MemoryStream();
        $ms->write($this->testData);

        $ms->close();

        $ms->read(1);
    }

    /**
     * @expectedException \Generics\Streams\StreamException
     */
    public function testCountClosed()
    {
        $ms = new MemoryStream();
        $ms->write($this->testData);

        $ms->close();

        $ms->count();
    }

    /**
     * @expectedException \Generics\Streams\StreamException
     */
    public function testResetClosed()
    {
        $ms = new MemoryStream();
        $ms->write($this->testData);

        $ms->close();

        $ms->reset();
    }

    /**
     * @expectedException \Generics\Streams\StreamException
     */
    public function testFlushClosed()
    {
        $ms = new MemoryStream();
        $ms->write($this->testData);

        $ms->close();

        $ms->flush();
    }
}
