<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Exceptions;

class VCardFieldException extends VCardIOException
{
    public static function invalidData(string $field)
    {
        return new self(sprintf('Invalid data for field %s', $field));
    }



    public static function emptyValue()
    {
        return new self('Empty value');
    }
}
