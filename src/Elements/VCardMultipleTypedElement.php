<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Elements;

use stdClass;

class VCardMultipleTypedElement extends VCardElement
{
    public function isMultiple(): bool
    {
        return true;
    }

    public function outputValue(): mixed
    {
        $object = new stdClass;
        $object->value = $this->inputValue;
        $object->type = implode(';', array_intersect($this->types, $this->restrictedTypes)) ?? 'default';

        return $object;
    }
}
