<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Exceptions;

class VCardParserException extends VCardIOException
{
    public static function fileNotFound(string $filePath)
    {
        return new self(sprintf("File %s doesn't exists", $filePath));
    }

    public static function fileUnreadable(string $filePath)
    {
        return new self(sprintf('File %s is not readable', $filePath));
    }

    public static function invalidObjects(?string $message = '')
    {
        return new self($message ?? 'Invalid vCards objects');
    }

    public static function unexpectedLine(int $lineNumber, string $lineMessage)
    {
        return new self(sprintf('Unexcpected %s on line %d', $lineMessage, $lineNumber));
    }

    public static function unreadableDataLine(int $lineNumber)
    {
        return new self(sprintf('Unreadable data on line %d', $lineNumber));
    }

    public static function invalidCharset(int $lineNumber, string $charset)
    {
        return new self(sprintf('Invalid charset %s on line %d', $charset, $lineNumber));
    }

    public static function emptyValue(int $lineNumber)
    {
        return new self('Empty value on line '.$lineNumber);
    }

    public static function unableToDecodeValue(string $valueType, string $rawValue)
    {
        return new self(sprintf('Unable to decode %s value : %s', $valueType, $rawValue));
    }

    public static function noVersionOnVCardStart(int $lineNumber)
    {
        return new self('No version on vCard start on line'.$lineNumber);
    }


}
