<?php
namespace Generics\Tests;

use Generics\Logger\SimpleLogger;
use Generics\Streams\FileInputStream;
use Generics\GenericsException;

class SimpleLoggerTest extends \PHPUnit_Framework_TestCase
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

    public function testSimpleLogger()
    {
        $logger = new SimpleLogger($this->logFileName);
        $logger->info("This message contains some {replacable} content", array(
            'replacable' => 'fine'
        ));

        $fis = new FileInputStream($logger->getFile());
        $content = $fis->read(1024);
        $fis->close();

        $this->assertRegExp('/This message contains some fine content$/', $content);
    }

    /**
     * @expectedException \Psr\Log\InvalidArgumentException
     */
    public function testLogLevelException()
    {
        $logger = new SimpleLogger($this->logFileName);
        $logger->log('an invalid level', "This message will not be logged, but an exception will be thrown");
    }

    public function testEmergency()
    {
        $logger = new SimpleLogger($this->logFileName);
        $logger->emergency("Some emergency log message");

        $fis = new FileInputStream($logger->getFile());
        $content = $fis->read(1024);
        $fis->close();
        $this->assertRegExp('/\[emerge\]: Some emergency log message$/', $content);
    }

    public function testAlert()
    {
        $logger = new SimpleLogger($this->logFileName);
        $logger->alert("Some alert log message");

        $fis = new FileInputStream($logger->getFile());
        $content = $fis->read(1024);
        $fis->close();
        $this->assertRegExp('/\[ alert\]: Some alert log message$/', $content);
    }

    public function testCritical()
    {
        $logger = new SimpleLogger($this->logFileName);
        $logger->critical("Some critical log message");

        $fis = new FileInputStream($logger->getFile());
        $content = $fis->read(1024);
        $fis->close();
        $this->assertRegExp('/\[critic\]: Some critical log message$/', $content);
    }

    public function testError()
    {
        $logger = new SimpleLogger($this->logFileName);
        $logger->error("Some error log message");

        $fis = new FileInputStream($logger->getFile());
        $content = $fis->read(1024);
        $this->assertRegExp('/\[ error\]: Some error log message$/', $content);
    }

    public function testWarning()
    {
        $logger = new SimpleLogger($this->logFileName);
        $logger->warning("Some warning log message");

        $fis = new FileInputStream($logger->getFile());
        $content = $fis->read(1024);
        $fis->close();
        $this->assertRegExp('/\[warnin\]: Some warning log message$/', $content);
    }

    public function testNotice()
    {
        $logger = new SimpleLogger($this->logFileName);
        $logger->notice("Some notice log message");

        $fis = new FileInputStream($logger->getFile());
        $content = $fis->read(1024);
        $fis->close();
        $this->assertRegExp('/\[notice\]: Some notice log message$/', $content);
    }

    public function testDebug()
    {
        $logger = new SimpleLogger($this->logFileName);
        $logger->debug("Some debug log message");

        $fis = new FileInputStream($logger->getFile());
        $content = $fis->read(1024);
        $fis->close();
        $this->assertRegExp('/\[ debug\]: Some debug log message$/', $content);
    }

    public function testRotate()
    {
        $logger = new SimpleLogger($this->logFileName, 1);

        $message = "Some log message to test rotation. To speed up the test this message must be exactly 128 bytes...";
        $logger->info($message);
        $fis = new FileInputStream($logger->getFile());
        $this->assertEquals(128, $fis->count());
        $fis->close();

        for ($i = 0; $i < 8192; $i ++) {
            $logger->info($message);
        }
        $fis = new FileInputStream($logger->getFile());
        $this->assertEquals(128, $fis->count());
        $fis->close();
    }

    public function testInvalidSize()
    {
        $logger = new SimpleLogger($this->logFileName, 0);
        $this->assertEquals(2, $logger->getMaxLogSize());
    }
}
