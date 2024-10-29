<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Explanatory;

use Pleb\VCardIO\Fields\AbstractField;

class SoundField extends AbstractField
{
    protected string $name = 'sound';

    protected bool $multiple = true;

    public function __construct(public string $sound, public array $attributes = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value, $attributes);
    }

    public function render(): mixed
    {
        return $this->sound;
    }

    public function __toString(): string
    {
        return $this->toString($this->sound, $this->attributes);
    }
}
