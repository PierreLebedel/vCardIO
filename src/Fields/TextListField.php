<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields;

use stdClass;

class TextListField extends AbstractField
{
    public function render(): mixed
    {
        $array = array_filter(array_map('trim', explode(',', $this->value)));

        if ($this->hasAttributes) {
            $response = new stdClass;
            $response->value = $array;
            $response->attributes = $this->attributes;

            return $response;
        }

        return $array;
    }

    public function relevantRender(): mixed
    {
        $response = $this->render();

        return ($response instanceof stdClass) ? $response->value : $response;
    }
}
