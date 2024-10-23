<?php

namespace Pleb\VCardIO;

use Pleb\VCardIO\Exceptions\VCardIOParseException;
use Pleb\VCardIO\Models\VCard;

class VCardParser
{
    /**
     * Parse vCard file and return array of VCard objects.
     *
     * @return array<VCard>
     */
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

    /**
     * Parse vCard data and return array of VCard objects.
     *
     * @return array<VCard>
     */
    public static function parseRaw(string $rawData): array
    {
        $vCards = [];

        return $vCards;
    }
}
