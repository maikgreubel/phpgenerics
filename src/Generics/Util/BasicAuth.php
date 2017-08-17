<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Util;

use Generics\GenericsException;
use Generics\Client\HttpStatus;

/**
 * This class provides http basic auth provider
 *
 * @author Maik Greubel <greubel@nkey.de>
 *        
 */
class BasicAuth
{

    /**
     * The basic auth user
     *
     * @var string
     */
    private $user;

    /**
     * The basic auth password
     *
     * @var string
     */
    private $password;

    /**
     * The list of files to skip authentication
     *
     * @var array
     */
    private $whitelist;

    /**
     * Realm name
     *
     * @var string
     */
    private $realm;

    /**
     * Create a new basic auth instance
     *
     * @param string $user
     *            The username
     * @param string $password
     *            The password
     * @param array $whitelist
     *            The list of files to skip authentication
     * @param string $realm
     *            The name of the realm
     */
    public function __construct($user, $password, $whitelist = array(), $realm = "Authentication realm")
    {
        $this->user = $user;
        $this->password = $password;
        $this->whitelist = $whitelist;
        $this->realm = $realm;
    }

    /**
     * Perform authentication
     *
     * @param string $file
     * @throws GenericsException
     * @return boolean
     */
    public function auth($file = '')
    {
        if (php_sapi_name() == 'cli') {
            throw new GenericsException("CLI does not support basic auth!");
        }
        
        if ($file && in_array($file, $this->whitelist)) {
            return true;
        }
        
        $user = null;
        $password = null;
        
        if (isset($_SERVER['PHP_AUTH_USER'])) {
            $user = $_SERVER['PHP_AUTH_USER'];
        }
        if (isset($_SERVER['PHP_AUTH_PW'])) {
            $password = $_SERVER['PHP_AUTH_PW'];
        }
        
        if ($user && $password && $user == $this->user && $password == $this->password) {
            return true;
        }
        
        $httpStatus = new HttpStatus(401, '1.0');
        header('WWW-Authenticate: Basic realm=' . $this->realm);
        header(sprintf('HTTP/%s', $httpStatus));
        echo "Forbidden!";
        return false;
    }
}
