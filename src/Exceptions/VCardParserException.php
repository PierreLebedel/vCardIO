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

    public static function invalidCharset(int $lineNumber, string $charset)
    {
        return new self(sprintf('Invalid charset %s on line %d', $charset, $lineNumber));
    }
}
