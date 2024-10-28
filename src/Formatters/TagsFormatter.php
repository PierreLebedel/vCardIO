<?php

namespace Pleb\VCardIO\Formatters;

class TagsFormatter
{

    public function __construct(public array $values, public array $attributes = [])
    {

    }

    public function __toString()
    {
        return implode(',', $this->values);
    }

}
