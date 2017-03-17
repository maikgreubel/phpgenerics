<?php

namespace Generics\Tests;

use Generics\Streams\FileInputStream;
use Generics\Streams\FileOutputStream;

class FileInputStreamTest extends \PHPUnit\Framework\TestCase
{

    private $fileName = 'input.dat';

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

    /**
     * @expectedException \Generics\FileNotFoundException
     */
    public function testNonExisting()
    {
        new FileInputStream("non-existing-file.ext");
    }

    /**
     * This will work without any exception
     * It will cause an exception in case of the FileInputStream will be opened by another process
     */
    public function testNoAccess()
    {
        if (file_exists($this->fileName)) {
            unlink($this->fileName);
        }
        $fis = new FileOutputStream($this->fileName);
        $this->assertTrue($fis->ready());
        $fis->lock();
        $this->assertTrue($fis->isLocked());

        new FileInputStream($this->fileName);
    }

    public function testLockUnlock()
    {
        $fis = new FileInputStream($this->fileName);
        $this->assertTrue($fis->ready());
        $fis->lock();
        $this->assertTrue($fis->isLocked());
        $fis->unlock();
        $this->assertFalse($fis->isLocked());
    }

    /**
     * @expectedException \Generics\LockException
     */
    public function testDoubleLock()
    {
        $fis = new FileInputStream($this->fileName);
        $fis->lock();
        $fis->lock();
    }

    /**
     * @expectedException \Generics\Streams\StreamException
     */
    public function testNotReady()
    {
        $fis = new FileInputStream($this->fileName);
        $fis->close();
        $fis->read();
    }
}
