<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Formatters;

class UriOrTextFormatter
{
    public function __construct(public string $value, public array $attributes = []) {}

    public function __toString()
    {
        return $this->value;
    }
}
