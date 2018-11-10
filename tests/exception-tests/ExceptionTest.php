<?php

namespace Generics\Tests;

use Generics\GenericsException;
use Generics\Socket\SocketException;
use Generics\Socket\InvalidUrlException;

class ExceptionTest extends \PHPUnit\Framework\TestCase
{

    public function testGenericException()
    {
        try {
            throw new GenericsException("This exception is part of a test. {placeholder}", array(
                'placeholder' => 'It is generics!'
            ));
        } catch (GenericsException $ex) {
            $this->assertEquals(0, $ex->getCode());
            $this->assertRegExp('/It is generics!$/', $ex->getMessage());
        }
    }

    public function testExceptionCode()
    {
        try {
            throw new SocketException(socket_strerror(10053), array(), 10053);
        } catch (GenericsException $ex) {
            $this->assertEquals(10053, $ex->getCode());
        }
    }

    /**
     * @expectedException \Generics\Socket\InvalidUrlException
     */
    public function testInvalidUrlException()
    {
        throw new InvalidUrlException("IUE thrown");
    }
}
