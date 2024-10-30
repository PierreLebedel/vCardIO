<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields;

use stdClass;

class TextField extends AbstractField
{
    public function render(): mixed
    {
        if (! $this->hasAttributes) {
            return $this->value;
        }

        $response = new stdClass;

        $response->value = $this->value;
        $response->attributes = $this->attributes;

        return $response;
    }

    public function getRelevantValue(): mixed
    {
        return $this->value;
    }
}
