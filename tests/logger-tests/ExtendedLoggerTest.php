<?php
namespace Generics\Tests;

use Generics\Logger\ExtendedLogger;
use Generics\GenericsException;
use Generics\Streams\FileInputStream;

class ExtendedLoggerTest extends \PHPUnit_Framework_TestCase
{

    private $logFileName = "test-logger.log";

    public function setUp()
    {
        if (file_exists($this->logFileName)) {
            unlink($this->logFileName);
        }
    }

    public function tearDown()
    {
        if (file_exists($this->logFileName)) {
            unlink($this->logFileName);
        }
    }

    public function testException()
    {
        $logger = new ExtendedLogger($this->logFileName);

        $logger->logException(new GenericsException("Some exception"));

        $fis = new FileInputStream($logger->getFile());
        $content = $fis->read(1024);
        $fis->close();

        $this->assertContains('[ alert]: (0): Some exception', $content);
    }

    public function testErrorException()
    {
        $logger = new ExtendedLogger($this->logFileName);

        $logger->logException(new \ErrorException("Some exception", 255));

        $fis = new FileInputStream($logger->getFile());
        $content = $fis->read(1024);
        $fis->close();

        $this->assertContains('[ error]: (255): Some exception', $content);
    }

    public function testRuntimeException()
    {
        $logger = new ExtendedLogger($this->logFileName);

        $logger->logException(new \RuntimeException("Some exception", 127));

        $fis = new FileInputStream($logger->getFile());
        $content = $fis->read(1024);
        $fis->close();

        $this->assertContains('[emerge]: (127): Some exception', $content);
    }

    public function testDump()
    {
        $logger = new ExtendedLogger($this->logFileName);
        $o = new \stdClass();
        $logger->dump($o);

        $fis = new FileInputStream($logger->getFile());
        $content = $fis->read(1024);
        $fis->close();

        $this->assertContains('stdClass', $content);
    }
}
