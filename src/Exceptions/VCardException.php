<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Exceptions;

class VCardException extends VCardIOException
{
    public static function invalidVersion(string $version)
    {
        return new self(sprintf("Invalid version :  %s", $version));
    }
}
