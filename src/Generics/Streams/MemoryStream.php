<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams;

use Generics\Resettable;

/**
 * This class provides a memory stream for both input and output
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class MemoryStream implements InputOutputStream, Resettable
{

    /**
     * The local memory buffer
     *
     * @var string
     */
    private $memory;

    /**
     * Current position in memory buffer
     *
     * @var int
     */
    private $current;

    /**
     * Whether it is possible to perform reading action
     *
     * @var boolean
     */
    private $ready;

    /**
     * Whether stream is closed
     *
     * @var boolean
     */
    private $closed;

    /**
     * Create a new MemoryStream
     *
     * @param InputStream $in
     *            optional existing input stream - will be copied
     */
    public function __construct(InputStream $in = null)
    {
        $this->memory = "";
        if ($in != null) {
            $copy = clone $in;
            $copy->reset();
            while ($copy->ready()) {
                $this->memory .= $copy->read();
            }
            $copy->close();
        }
        $this->current = 0;
        $this->ready = true;
        $this->closed = false;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\Stream::close()
     */
    public function close()
    {
        unset($this->memory);
        $this->current = 0;
        $this->ready = false;
        $this->closed = true;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\Stream::ready()
     */
    public function ready()
    {
        return $this->ready;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\OutputStream::write()
     */
    public function write($buffer)
    {
        if ($this->closed) {
            throw new StreamException("Stream is not open");
        }
        $this->memory .= $buffer;
        $this->ready = true;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\InputStream::read()
     */
    public function read($length = 1)
    {
        if ($this->closed) {
            throw new StreamException("Stream is not open");
        }

        if (strlen($this->memory) <= $this->current) {
            $this->ready = false;
            return "";
        }

        if (strlen($this->memory) - $this->current < $length) {
            $length = strlen($this->memory) - $this->current;
        }

        $out = substr($this->memory, $this->current, $length);
        $this->current += $length;

        if ($this->current == strlen($this->memory)) {
            $this->ready = false;
        }

        return $out;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Countable::count()
     */
    public function count()
    {
        if ($this->closed) {
            throw new StreamException("Stream is not open");
        }
        return strlen($this->memory);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Resettable::reset()
     */
    public function reset()
    {
        if ($this->closed) {
            throw new StreamException("Stream is not open");
        }
        $this->current = 0;
        $this->ready = true;
    }

    /**
     * Write to stream by interpolation of context vars into a string
     *
     * @param string $string
     *            The string to interpolate, may contains placeholders in format {placeholder}.
     * @param array $context
     *            The context array containing the associative replacers and its values.
     */
    public function interpolate($string, array $context)
    {
        $replacers = array();
        foreach ($context as $key => $value) {
            $replacers['{' . $key . '}'] = $value;
        }
        $this->write(strtr($string, $replacers));
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\OutputStream::isWriteable()
     */
    public function isWriteable()
    {
        return true;
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Generics\Streams\OutputStream::flush()
     */
    public function flush()
    {
        if ($this->closed) {
            throw new StreamException("Stream is not open");
        }

        unset($this->memory);
        $this->reset();
    }
}
