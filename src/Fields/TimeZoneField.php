<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields;

use DateTimeZone;
use stdClass;

class TimeZoneField extends AbstractField
{
    public function render(): mixed
    {
        $timezone = new DateTimeZone($this->value) ?? null;

        $response = new stdClass;

        $response->timeZone = $timezone;
        $response->name = $timezone?->getName() ?? null;

        if ($this->hasAttributes) {
            $response->attributes = $this->attributes;
        }

        return $response;
    }

    public function getRelevantValue(): mixed
    {
        return $this->render()->timeZone;
    }
}
