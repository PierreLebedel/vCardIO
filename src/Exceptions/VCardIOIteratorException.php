<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Exceptions;

class VCardIOIteratorException extends VCardIOException
{
    public static function invalidIndex(?string $message = null)
    {
        return new self($message ?? 'Invalid index');
    }
}
