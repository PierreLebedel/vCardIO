<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Exceptions;

class VCardParserException extends AbstractVCardException
{
    public static function fileNotFound(string $filePath)
    {
        return new self(sprintf("File %s doesn't exists", $filePath));
    }

    public static function fileUnreadable(string $filePath)
    {
        return new self(sprintf('File %s is not readable', $filePath));
    }

    public static function invalidFormat(?string $message = '')
    {
        return new self($message ?? 'Invalid format');
    }

    public static function unexpectedLine(int $lineNumber, string $lineMessage)
    {
        return new self(sprintf('Unexpected %s on line %d', $lineMessage, $lineNumber));
    }

    public static function noVersionOnVCardStart(int $lineNumber)
    {
        return new self('No version on vCard start on line '.$lineNumber);
    }

    public static function invalidVersion(string $version, int $lineNumber)
    {
        return new self(sprintf('Invalid version %s on line %d', $version, $lineNumber));
    }
}
