<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Exceptions;

class VCardCollectionArrayAccessException extends VCardIOException
{
    public static function invalidIndex(?string $message = null)
    {
        return new self($message ?? 'Invalid index');
    }

    public static function invalidValue(?string $message = null)
    {
        return new self($message ?? 'Invalid value');
    }
}
