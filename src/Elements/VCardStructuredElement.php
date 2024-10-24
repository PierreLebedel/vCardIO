<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Elements;

use Pleb\VCardIO\Contracts\VCardStructuredElementInterface;

abstract class VCardStructuredElement extends VCardElement implements VCardStructuredElementInterface
{
    public function __construct(public string $inputValue, public array $types = [])
    {
        $values = explode(';', $this->inputValue, count($this->definition()));
        foreach ($values as $k => $v) {
            $this->{$this->definition()[$k]} = $v;
        }
    }

    public function outputValue(): mixed
    {
        $object = new \stdClass;
        foreach ($this->definition() as $k => $v) {
            $object->{$v} = $this->{$v};
        }

        if (! empty($this->restrictedTypes)) {
            $object->type = implode(';', array_intersect($this->types, $this->restrictedTypes)) ?? 'default';
        }

        return $object;
    }
}
