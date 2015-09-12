<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams;

use Generics\FileExistsException;
use Generics\NoAccessException;
use Generics\LockException;
use Generics\Lockable;

/**
 * This class provides a file output stream.
 *
 * @author Maik
 */
class FileOutputStream implements OutputStream, Lockable
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
     * Whether resource is locked
     *
     * @var boolean
     */
    private $locked;

    /**
     * Create a new FileOutputStream
     *
     * @param string $file
     *            The absolute (or relative) path to file to write into.
     * @param boolean $append
     *            Whether to append the data to an existing file.
     * @throws FileExistsException will be thrown in case of file exists and append is set to false.
     * @throws NoAccessException will be thrown in case of it is not possible to write to file.
     */
    public function __construct($file, $append = false)
    {
        $this->locked = false;

        $mode = "wb";

        if (file_exists($file)) {
            if (! $append) {
                throw new FileExistsException("File $file already exists!");
            }

            if (! is_writable($file)) {
                throw new NoAccessException("Cannot write to file $file");
            }
            $mode = "ab";
        } else {
            if (! is_writable(dirname($file))) {
                throw new NoAccessException("Cannot write to file {file}", array(
                    'file' => $file
                ));
            }
        }

        $this->handle = fopen($file, $mode);

        if (! $this->ready()) {
            throw new StreamException("Could not open {file} for writing", array(
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
     * @see \Generics\Streams\Stream::ready()
     */
    public function ready()
    {
        return is_resource($this->handle);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\OutputStream::write()
     */
    public function write($buffer)
    {
        if (! $this->ready()) {
            throw new StreamException("Stream is not open!");
        }

        if ($buffer instanceof InputStream) {
            $in = clone $buffer;
            assert($in instanceof InputStream);
            while ($in->ready()) {
                $buf = $in->read(1024);
                if (fwrite($this->handle, $buf) != strlen($buf)) {
                    throw new StreamException("Could not write memory stream into file");
                }
            }
        } else {
            $buffer = strval($buffer);
            if (fwrite($this->handle, $buffer) != strlen($buffer)) {
                throw new StreamException("Could not write buffer into file");
            }
            fflush($this->handle);
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\Stream::close()
     */
    public function close()
    {
        if (is_resource($this->handle)) {
            fclose($this->handle);
            $this->handle = null;
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Countable::count()
     */
    public function count()
    {
        if (! $this->ready()) {
            throw new StreamException("Stream is not open!");
        }

        return ftell($this->handle);
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

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\OutputStream::isWriteable()
     */
    public function isWriteable()
    {
        return $this->ready();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\OutputStream::flush()
     */
    public function flush()
    {
        if (! $this->ready()) {
            throw new StreamException("Stream is not open!");
        }

        fflush($this->handle);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Lockable::lock()
     */
    public function lock()
    {
        if ($this->locked || flock($this->handle, LOCK_EX) === false) {
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
}
