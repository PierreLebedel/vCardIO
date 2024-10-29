<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Exceptions;

class VCardBuilderException extends AbstractVCardException
{
    public static function wrongValue(string $propertyName, mixed $value)
    {
        return new self(sprintf('Wrong value for field %s', $propertyName));
    }
}
