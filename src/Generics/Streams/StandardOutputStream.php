<?php
/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams;

use Generics\Streams\Interceptor\StreamInterceptor;
use Countable;

/**
 * This class provides a stream for standard output
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
class StandardOutputStream implements OutputStream
{

    /**
     * Interceptor
     * 
     * @var StreamInterceptor
     */
    private $interceptor;

    /**
     * The standard out channel
     * 
     * @var resource
     */
    private $stdout;

    /**
     * Create a new instance of StandardOutputStream
     */
    public function __construct()
    {
        $this->open();
    }

    /**
     * Opens a new standard output channel
     */
    private function open()
    {
        $this->stdout = fopen("php://stdout", "w");
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\Stream::isOpen()
     */
    public function isOpen(): bool
    {
        return is_resource($this->stdout);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\OutputStream::flush()
     */
    public function flush()
    {
        if ($this->isOpen()) {
            fflush($this->stdout);
        }
        if ($this->interceptor instanceof StreamInterceptor) {
            $this->interceptor->reset();
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\Stream::ready()
     */
    public function ready(): bool
    {
        return $this->isOpen();
    }

    /**
     *
     * {@inheritdoc}
     * @see Countable::count()
     */
    public function count()
    {
        return 0;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Resettable::reset()
     */
    public function reset()
    {
        $this->close();
        $this->open();
        
        if ($this->interceptor instanceof StreamInterceptor) {
            $this->interceptor->reset();
            $this->setInterceptor($this->interceptor);
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\OutputStream::write()
     */
    public function write($buffer)
    {
        if ($this->isWriteable()) {
            fwrite($this->stdout, $buffer);
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\Stream::close()
     */
    public function close()
    {
        if ($this->isOpen()) {
            fclose($this->stdout);
            $this->stdout = null;
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Generics\Streams\OutputStream::isWriteable()
     */
    public function isWriteable(): bool
    {
        return $this->isOpen();
    }

    /**
     * Apply a stream interceptor
     *
     * @param StreamInterceptor $interceptor
     */
    public function setInterceptor(StreamInterceptor $interceptor)
    {
        $this->interceptor = $interceptor;
        stream_filter_append($this->stdout, $interceptor->getFilterName());
    }
}