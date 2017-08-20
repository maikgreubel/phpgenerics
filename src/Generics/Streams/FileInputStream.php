<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams;

use Generics\FileNotFoundException;
use Generics\LockException;
use Generics\Lockable;

/**
 * This class provides an input stream for files.
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class FileInputStream implements InputStream, Lockable
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
     * @var bool
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
     * Cleanup (e.g.
     * release lock)
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
     *
     * {@inheritdoc}
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
     *
     * {@inheritdoc}
     * @see \Generics\Streams\Stream::ready()
     */
    public function ready(): bool
    {
        return is_resource($this->handle) && ! feof($this->handle);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\InputStream::read()
     */
    public function read($length = 1, $offset = null): string
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
     *
     * {@inheritdoc}
     * @see \Countable::count()
     */
    public function count(): int
    {
        $stat = fstat($this->handle);
        return $stat['size'];
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\InputStream::reset()
     */
    public function reset()
    {
        fseek($this->handle, 0, SEEK_SET);
    }

    /**
     *
     * {@inheritdoc}
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
     *
     * {@inheritdoc}
     * @see \Generics\Lockable::unlock()
     */
    public function unlock()
    {
        if (! $this->locked || flock($this->handle, LOCK_UN) === false) {
            throw new LockException("Could not release lock");
        }
        $this->locked = false;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Lockable::isLocked()
     */
    public function isLocked(): bool
    {
        return $this->locked;
    }

    /**
     * Retrieve the file path and name
     *
     * @return string
     */
    public function __toString(): string
    {
        return realpath($this->fileName);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\Stream::isOpen()
     */
    public function isOpen(): bool
    {
        return is_resource($this->handle);
    }
}
