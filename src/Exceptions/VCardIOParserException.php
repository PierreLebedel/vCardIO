<?php

namespace Pleb\VCardIO\Exceptions;

class VCardIOParserException extends VCardIOException
{
    public static function fileNotFound(string $filePath)
    {
        return new self(sprintf("File %s doesn't exists", $filePath));
    }

    public static function fileUnreadable(string $filePath)
    {
        return new self(sprintf('File %s is not readable', $filePath));
    }
}
