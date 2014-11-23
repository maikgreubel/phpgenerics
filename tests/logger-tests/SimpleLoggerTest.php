<?php
require_once 'Generics/Logger/SimpleLogger.php';
require_once 'Generics/Streams/FileInputStream.php';

use Generics\Logger\SimpleLogger;
use Generics\Streams\FileInputStream;

class SimpleLoggerTest extends PHPUnit_Framework_TestCase
{

    private $logFileName = "test-logger.log";

    public function setUp()
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
}