<?php
namespace Generics\Tests;

use Generics\Client\HttpStatus;

class HttpStatusTest extends \PHPUnit\Framework\TestCase
{
    public function testHttpStatusSimple()
    {
        $status = HttpStatus::getStatus(200);

        $this->assertEquals('OK', $status);
    }

    public function testHttpStatusParser()
    {
        $statLine = 'HTTP/1.1 500 Internal Server Error';
        $status = HttpStatus::parseStatus($statLine);

        $this->assertEquals(500, $status->getCode());
        $this->assertEquals('HTTP/1.1', $status->getProtocol());
        $this->assertEquals($statLine, $status->toStatusLine());

        $this->assertEquals($statLine, strval($status));
    }
}
