<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams;

use Generics\Util\Interpolator;

/**
 * This class provides a memory stream for both input and output
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class MemoryStream implements InputOutputStream
{
    use Interpolator {
        interpolate as tinterpolate;
    }

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
     *
     * {@inheritdoc}
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
     *
     * {@inheritdoc}
     * @see \Generics\Streams\Stream::ready()
     */
    public function ready(): bool
    {
        return $this->ready;
    }

    /**
     *
     * {@inheritdoc}
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
     *
     * {@inheritdoc}
     * @see \Generics\Streams\InputStream::read()
     */
    public function read($length = 1, $offset = null): string
    {
        if ($this->closed) {
            throw new StreamException("Stream is not open");
        }
        
        if ($offset !== null) {
            $this->current = intval($offset);
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
     *
     * {@inheritdoc}
     * @see \Countable::count()
     */
    public function count(): int
    {
        if ($this->closed) {
            throw new StreamException("Stream is not open");
        }
        if (! isset($this->memory)) {
            return 0;
        }
        return strlen($this->memory);
    }

    /**
     *
     * {@inheritdoc}
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
        $this->write($this->tinterpolate($string, $context));
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\OutputStream::isWriteable()
     */
    public function isWriteable(): bool
    {
        return true;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\OutputStream::flush()
     */
    public function flush()
    {
        if ($this->closed) {
            throw new StreamException("Stream is not open");
        }
        
        unset($this->memory);
        $this->memory = "";
        $this->reset();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\Stream::isOpen()
     */
    public function isOpen(): bool
    {
        return true;
    }
}
