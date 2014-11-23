<?php

namespace Generics\Tests;

use Generics\Streams\FileInputStream;

class FileInputStreamTest extends \PHPUnit_Framework_TestCase
{

    private $fileName = 'sample.dat';

    private $testData = "Well, this content is only needed for testing the framework. Don't expect to much...";

    public function setUp()
    {
        file_put_contents($this->fileName, $this->testData);
    }

    public function tearDown()
    {
        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }
    }

    public function testSimple()
    {
        $fis = new FileInputStream($this->fileName);

        $this->assertEquals(strlen($this->testData), $fis->count());

        $in = "";

        while ($fis->ready()) {
            $in .= $fis->read();
        }

        $this->assertEquals($this->testData, $in);

        $fis->reset();

        $in = $fis->read(1024);

        $this->assertEquals($this->testData, $in);

        $this->assertFalse($fis->ready());
    }
}
