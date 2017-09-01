<?php
namespace Generics\Util;

use Generics\DeprecatedException;
use Generics\NoticeException;
use Generics\RecoverableErrorException;
use Generics\UserDeprecatedException;
use Generics\UserErrorException;
use Generics\UserNoticeException;
use Generics\UserWarningException;
use Generics\WarningException;
use ErrorException;

class ExceptionErrorHandler
{
    public function __construct()
    {
        $this->setErrorHandler();
    }
    
    private function setErrorHandler()
    {
        set_error_handler(array($this, 'errorHandler'), E_ALL | E_STRICT);
    }
    
    public function errorHandler(int $err_severity, string $err_msg, string $err_file, int $err_line, array $err_context)
    {
        if (0 === error_reporting()) { 
            return false;
        }
        
        switch($err_severity)
        {
            case E_WARNING:  throw new WarningException  ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_NOTICE:  throw new NoticeException  ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_USER_ERROR:  throw new UserErrorException  ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_USER_WARNING:  throw new UserWarningException  ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_USER_NOTICE:  throw new UserNoticeException  ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_RECOVERABLE_ERROR:  throw new RecoverableErrorException ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_DEPRECATED:  throw new DeprecatedException  ($err_msg, 0, $err_severity, $err_file, $err_line);
            case E_USER_DEPRECATED:  throw new UserDeprecatedException  ($err_msg, 0, $err_severity, $err_file, $err_line);
            default:            throw new ErrorException  ($err_msg, 0, $err_severity, $err_file, $err_line);
        }
    }
}