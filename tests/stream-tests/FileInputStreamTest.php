<?php
require_once 'Generics/Streams/FileInputStream.php';

use Generics\Streams\FileInputStream;

class FileInputStreamTest extends PHPUnit_Framework_TestCase
{
  private $testData = "Well, this content is only needed for testing the framework. Don't expect to much...";
  
  public function testSimple()
  {
    $fis = new FileInputStream('tests/sample.dat');

    $this->assertEquals(strlen($this->testData), $fis->count());
    
    $in = "";
    
    while($fis->ready())
    {
      $in .= $fis->read();
    }
    
    $this->assertEquals($this->testData, $in);
    
    $fis->reset();
    
    $in = $fis->read(1024);

    $this->assertEquals($this->testData, $in);
    
    $this->assertFalse($fis->ready());
  }
} 