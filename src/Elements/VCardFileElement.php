<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Elements;

use stdClass;

class VCardFileElement extends VCardElement
{
    public function outputValue(): mixed
    {

        $object = new stdClass;
        $object->value = $this->inputValue;
        $object->type = !empty($this->types) ? implode(';', $this->types) : 'default';

        return $object;
    }
}
