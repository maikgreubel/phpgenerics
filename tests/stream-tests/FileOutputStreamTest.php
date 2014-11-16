<?php
require_once 'Generics/Streams/FileOutputStream.php';
require_once 'Generics/Streams/FileOutputStream.php';
require_once 'Generics/FileExistsException.php';

use Generics\Streams\FileOutputStream;
use Generics\Streams\FileInputStream;

class FileOutputStreamTest extends PHPUnit_Framework_TestCase
{
  private $testFile = 'tests/output.dat';
  
  private $testData = "Only some test data; Purpose is to test the file output stream.";
  
  public function setUp()
  {
    file_put_contents($this->testFile, $this->testData);
  }
  
  public function tearDown()
  {
    unlink($this->testFile);
  }
        
  public function testSimple()
  {
    if(file_exists($this->testFile))
    {
      unlink($this->testFile);
    }
    
    $fos = new FileOutputStream($this->testFile);

    $fos->write($this->testData);
    $fos->close();
    
    $fis = new FileInputStream($this->testFile);

    $in = "";
    while($fis->ready())
    {
      $in .= $fis->read(1024);
    }
    
    $fis->close();
    
    $this->assertEquals($this->testData, $in);
  }
  
  /**
   * @expectedException \Generics\FileExistsException
   */
  public function testError()
  {
    $fos = new FileOutputStream($this->testFile);
  }
  
  public function testAppending()
  {
    $fos = new FileOutputStream($this->testFile, true);
    $fos->write($this->testData);
    
    $fis = new FileInputStream($this->testFile);
    
    $in = "";
    while($fis->ready())
    {
      $in .= $fis->read(1024);
    }

    $this->assertEquals("{$this->testData}{$this->testData}", $in);
    
    $fis->close();
    $fos->close();
    
  }
}