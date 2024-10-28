<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Identification;

use Pleb\VCardIO\Fields\AbstractField;

class PhotoField extends AbstractField
{
    protected string $name = 'photo';

    protected bool $multiple = false;

    public function __construct(public string $photo, public array $attributes = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value, $attributes);
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
