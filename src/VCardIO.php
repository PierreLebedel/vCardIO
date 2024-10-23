<?php

namespace Pleb\VCardIO;

use Pleb\VCardIO\Exceptions\VCardIOParseException;

class VCardIO
{
    public static function parseFile(string $filePath): array
    {
        if (! file_exists($filePath)) {
            throw VCardIOParseException::fileNotFound($filePath);
        }
        if (! is_readable($filePath)) {
            throw VCardIOParseException::fileUnreadable($filePath);
        }

        return self::parseRaw(file_get_contents($filePath));
    }

    public static function parseRaw(string $rawData): array
    {
        $vCards = [];


        return $vCards;
    }
}
