<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Exceptions;

class VCardBuilderException extends VCardIOException
{
    public static function notSingularField(string $field)
    {
        return new self(sprintf("Field %s isn't singular", $field));
    }

    public static function notMultipleField(string $field)
    {
        return new self(sprintf("Field %s isn't multiple", $field));
    }

    public static function wrongStringValue(string $field, string $value)
    {
        return new self(sprintf('Wrong value %s for field %s', $value, $field));
    }
}
