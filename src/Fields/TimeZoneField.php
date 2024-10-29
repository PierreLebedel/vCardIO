<?php

namespace Pleb\VCardIO\Fields;

use stdClass;
use DateTimeZone;
use Pleb\VCardIO\Fields\AbstractField;

class TimeZoneField extends AbstractField
{

    public function render() :mixed
    {
        $timezone = new DateTimeZone($this->value) ?? null;

        $response = new stdClass();

        $response->timeZone = $timezone;
        $response->name = $timezone?->getName() ?? null;

        if( $this->hasAttributes ){
            $response->attributes = $this->attributes;
        }

        return $response;
    }

}
