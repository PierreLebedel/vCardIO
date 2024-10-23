<?php

namespace Pleb\VCardIO\Exceptions;

class VCardIOParseException extends \Exception
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
