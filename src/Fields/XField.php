<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields;

use stdClass;

class XField extends AbstractField
{
    public ?string $name = null;

    public function setFormat(string $format): void
    {
        $format = strtolower($format);
        if (substr($format, 0, 2) == 'x-') {
            $format = substr($format, 2);
        }

        $this->name = $format;
    }

    public function render(): mixed
    {
        $response = new stdClass;

        $response->name = $this->name;
        $response->value = $this->value;
        $response->attributes = $this->attributes;

        return $response;
    }

    public function relevantRender(): mixed
    {
        return $this->render()->value;
    }
}
