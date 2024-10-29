<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields;

use stdClass;

class SexField extends AbstractField
{
    public function render(): mixed
    {
        $data = explode(';', $this->value);

        $letter = strtoupper($data[0]);
        $description = $data[1] ?? null;

        if (! in_array($letter, ['A', 'M', 'F', 'O', 'N', 'U'])) {
            $letter = null;
        }

        $response = new stdClass;

        $response->letter = $letter;
        $response->description = $description;

        if ($this->hasAttributes) {
            $response->attributes = $this->attributes;
        }

        return $response;
    }
}
