<?php

namespace Pleb\VCardIO\Formatters;

class UriFormatter
{

    public function __construct(public string $value, public array $attributes = [])
    {

    }

    public function __toString()
    {
        return $this->value;
    }

}
