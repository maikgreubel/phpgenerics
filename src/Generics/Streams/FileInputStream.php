<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams;

use \Generics\FileNotFoundException;
use \Generics\Resettable;
use \Generics\Lockable;
use \Generics\LockException;

/**
 * This class provides an input stream for files.
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class FileInputStream implements InputStream, Resettable, Lockable
{

    /**
     * The file handle
     *
     * @var resource
     */
    private $handle;

    /**
     * The absolute file path and name
     *
     * @var string
     */
    private $fileName;

    /**
     * Whether the access is locked
     *
     * @var boolean
     */
    private $locked;

    /**
     * Create a new FileInputStream
     *
     * @param string $file
     *            The absolute (or relative) path to the file to open
     * @throws FileNotFoundException
     */
    public function __construct($file)
    {
        if (! file_exists($file)) {
            throw new FileNotFoundException("File {file} could not be found", array(
                'file' => $file
            ));
        }

        $this->handle = fopen($file, "rb");

        if (! $this->ready()) {
            throw new StreamException("Could not open {file} for reading", array(
                'file' => $file
            ));
        }

        $this->fileName = $file;
    }

    /**
     * Cleanup (e.g. release lock)
     */
    public function __destruct()
    {
        try {
            if ($this->locked) {
                $this->unlock();
            }
        } catch (\Generics\GenericsException $ex) {
            // Do nothing
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\Stream::close()
     */
    public function close()
    {
        if ($this->handle != null) {
            fclose($this->handle);
            $this->handle = null;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\Stream::ready()
     */
    public function ready()
    {
        return is_resource($this->handle) && ! feof($this->handle);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\InputStream::read()
     */
    public function read($length = 1, $offset = null)
    {
        if (! $this->ready()) {
            throw new StreamException("Stream is not ready!");
        }

        if ($offset !== null && intval($offset) > 0) {
            if (fseek($this->handle, $offset, SEEK_SET) != 0) {
                throw new StreamException("Could not set offset!");
            }
        }

        return fread($this->handle, $length);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Countable::count()
     */
    public function count()
    {
        $stat = fstat($this->handle);
        return $stat['size'];
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\InputStream::reset()
     */
    public function reset()
    {
        fseek($this->handle, 0, SEEK_SET);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Lockable::lock()
     */
    public function lock()
    {
        if ($this->locked || flock($this->handle, LOCK_SH) === false) {
            throw new LockException("Could not acquire lock");
        }
        $this->locked = true;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Lockable::unlock()
     */
    public function unlock()
    {
        if (!$this->locked || flock($this->handle, LOCK_UN) === false) {
            throw new LockException("Could not release lock");
        }
        $this->locked = false;
    }

    /**
     * Retrieve the file path and name
     *
     * @return string
     */
    public function __toString()
    {
        return realpath($this->fileName);
    }
}
