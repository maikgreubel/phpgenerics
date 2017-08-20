<?php
/**
 * This file is part of the PHP Generics package.
 *
 * @package Generics
 */
namespace Generics\Client;

/**
 * This class provides helper for status code to status message conversion
 *
 * @author Maik
 */
class HttpStatus
{

    const STATUS_200 = 'OK';

    const STATUS_301 = 'Moved Permanently';

    const STATUS_302 = 'Found';

    const STATUS_400 = 'Bad Request';

    const STATUS_401 = 'Unauthorized';

    const STATUS_403 = 'Forbidden';

    const STATUS_404 = 'Not Found';

    const STATUS_500 = 'Internal Server Error';

    const STATUS_501 = 'Not Implemented';

    const STATUS_502 = 'Bad Gateway';

    const STATUS_503 = 'Service Unavailable';

    const STATUS_505 = 'HTTP Version Not Supported';

    /**
     * The status code
     *
     * @var int
     */
    private $code;

    /**
     * The protocol version
     *
     * @var string
     */
    private $proto;

    /**
     * Create a new HttpStatus instance
     *
     * @param int $code
     *            The status code
     * @param string $protocolVersion
     *            The version of Http protocol (e.g. 1.0)
     */
    public function __construct($code, $protocolVersion = '1.1')
    {
        $this->code = $code;
        $this->proto = $protocolVersion;
    }

    /**
     * Retrieve the status message for a particular code
     *
     * @param int $code
     *
     * @return string The status message
     */
    public static function getStatus($code): string
    {
        $prop = sprintf("Generics\\Client\\HttpStatus::STATUS_%d", $code);
        return constant($prop);
    }

    /**
     * Parse the status line into its parts
     *
     * @param string $statusLine
     *
     * @return HttpStatus
     */
    public static function parseStatus($statusLine): HttpStatus
    {
        list ($proto, $code) = sscanf($statusLine, "%s %d %s");
        return new HttpStatus($code, $proto);
    }

    /**
     * Retrieve the status line
     *
     * @return string The status line according RFC
     * @see http://greenbytes.de/tech/webdav/draft-ietf-httpbis-p1-messaging-latest.html#rfc.section.3.1.2
     */
    public function toStatusLine(): string
    {
        return sprintf("%s %d %s", $this->proto, $this->code, self::getStatus($this->code));
    }

    /**
     * Retrieve the status as string
     * It is a wrapper for
     *
     * @see \Generics\Client\HttpStatus::toStatusLine()
     *
     * @return string
     */
    public function __toString(): string
    {
        return $this->toStatusLine();
    }

    /**
     * Get the status code
     *
     * @return int
     */
    public function getCode(): int
    {
        return $this->code;
    }

    /**
     * Get the protocol including version
     *
     * @return string
     */
    public function getProtocol(): string
    {
        return $this->proto;
    }
}
