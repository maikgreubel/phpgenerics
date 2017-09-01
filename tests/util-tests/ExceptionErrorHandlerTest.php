<?php
namespace Generics\Tests;

use PHPUnit\Framework\TestCase;
use Generics\NoticeException;
use Generics\Util\ExceptionErrorHandler;
use Generics\WarningException;
use Generics\UserErrorException;
use Generics\UserWarningException;
use Generics\UserNoticeException;
use Generics\RecoverableErrorException;
use Generics\DeprecatedException;
use Generics\UserDeprecatedException;

class A {
}

class B {
    function B()
    {
    }
}

class C {
    function bar()
    {
        
    }
}

class ExceptionErrorHandlerTest extends TestCase
{
    protected function setUp()
    {
        new ExceptionErrorHandler();
    }
    
    /**
     * @test
     */
    public function testNoticeException()
    {
        $this->expectException(NoticeException::class);
        
        echo $a['b'];
    }
    
    /**
     * @test
     */
    public function testWarningException()
    {
        $this->expectException(WarningException::class);
        
        trigger_error('A warning', E_WARNING);
    }
    
    /**
     * @test
     */
    public function testUserError()
    {
        $this->expectException(UserErrorException::class);
        
        trigger_error('A user error', E_USER_ERROR);
    }

    /**
     * @test
     */
    public function testUserWarning()
    {
        $this->expectException(UserWarningException::class);
        
        trigger_error('A user warning', E_USER_WARNING);
    }
    
    /**
     * @test
     */
    public function testUserNotice()
    {
        $this->expectException(UserNoticeException::class);
        
        trigger_error('A user notice', E_USER_NOTICE);
    }

    /**
     * @test
     */
    public function testRecoverableError()
    {
        $this->expectException(RecoverableErrorException::class);
        
        $a = new A();
        printf("%s\n", $a);
    }

    /**
     * @test
     */
    public function testDeprecated()
    {
        $this->expectException(DeprecatedException::class);
        
        C::bar();
    }

    /**
     * @test
     */
    public function testUserDeprecated()
    {
        $this->expectException(UserDeprecatedException::class);
        
        trigger_error('A user deprecated', E_USER_DEPRECATED);
    }
}