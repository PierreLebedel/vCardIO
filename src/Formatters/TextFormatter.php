<?php

namespace Pleb\VCardIO\Formatters;

class TextFormatter
{

    public function __construct(public string $value, public array $attributes = [])
    {

    }

    public function __toString()
    {
        return $this->value;
    }

}
