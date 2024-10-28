<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Identification;

use Pleb\VCardIO\Fields\AbstractField;

class PhotoField extends AbstractField
{
    protected string $name = 'photo';

    protected bool $multiple = true;

    public function __construct(public string $photo, public array $attributes = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value, $attributes);
    }

    public static function getPossibleAttributes(): array
    {
        return [
            'altid',
            'type',
            'mediatype',
            'pref',
            'pid',
        ];
    }

    public function render(): mixed
    {
        return $this->photo;
    }

    public function __toString(): string
    {
        return $this->toString($this->photo, $this->attributes);
    }
}
