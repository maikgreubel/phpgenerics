<?php

/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Streams;

/**
 * This interfaces describes a HTTP Stream implementation
 *
 * @author Maik Greubel <greubel@nkey.de>
 */
interface HttpStream extends InputOutputStream
{

    /**
     * Retrieve all headers as array
     *
     * @return array
     */
    public function getHeaders(): array;

    /**
     * Set a particular header to a corresponding value
     *
     * @param string $headerName
     *            The header to set
     * @param string $headerValue
     *            The value to set
     */
    public function setHeader($headerName, $headerValue);

    /**
     * Retrieve the payload (http body)
     *
     * @return InputOutputStream The payload as stream
     *        
     * @throws \Generics\Streams\StreamException
     */
    public function getPayload(): InputOutputStream;

    /**
     * Append the payload (http body)
     *
     * @param InputStream $payload
     *            The payload to append
     *            
     * @throws \Generics\Streams\StreamException
     */
    public function appendPayload(InputStream $payload);

    /**
     * Start the request
     *
     * @param string $requestType
     *            The type of request (GET, POST, etc.)
     *            
     * @throws \Generics\Client\HttpException
     * @throws \Generics\Socket\SocketException
     */
    public function request(string $requestType);
}
