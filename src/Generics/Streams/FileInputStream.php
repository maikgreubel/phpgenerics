<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams;

use \Generics\FileNotFoundException;
use \Generics\Resettable;

/**
 * This class provides an input stream for files.
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class FileInputStream implements InputStream, Resettable
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
    public function read($length = 1)
    {
        if (! $this->ready()) {
            throw new StreamException("Stream is not ready!");
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
     * Retrieve the file path and name
     *
     * @return string
     */
    public function __toString()
    {
        return realpath($this->fileName);
    }
}
