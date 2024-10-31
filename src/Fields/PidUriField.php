<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields;

use stdClass;

class PidUriField extends AbstractField
{
    public function render(): mixed
    {
        $value = explode(',', $this->value)[0];

        $parts = explode(';', $value, 2);

        $response = new stdClass;

        $response->pid = $parts[0] ?? null;
        $response->uri = $parts[1] ?? null;

        if ($this->hasAttributes) {
            $response->attributes = $this->attributes;
        }

        return $response;
    }

    public function relevantRender(): mixed
    {
        $response = $this->render();

        return [$response->pid => $response->uri];
    }
}
