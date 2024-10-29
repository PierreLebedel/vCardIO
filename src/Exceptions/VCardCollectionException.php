<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Exceptions;

class VCardCollectionException extends AbstractVCardException
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
