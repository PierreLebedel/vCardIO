<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Elements;

use stdClass;

class VCardGeoElement extends VCardElement
{
    public function outputValue(): mixed
    {
        @[$latitude, $longitude] = explode(';', $this->inputValue, 3);

        $latitude = str_replace('geo:', '', $latitude);
        $longitude = str_replace('geo:', '', $longitude);

        if (empty($latitude) || empty($longitude)) {
            return null;
        }

        $object = new stdClass;
        $object->latitude = $latitude;
        $object->longitude = $longitude;

        return $object;
    }
}
