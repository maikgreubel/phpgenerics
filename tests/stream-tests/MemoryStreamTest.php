<?php

namespace Generics\Tests;

use Generics\Streams\MemoryStream;
use Generics\Streams\FileInputStream;

class MemoryStreamTest extends \PHPUnit_Framework_TestCase
{

    private $testFile = 'sample.dat';

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
}
