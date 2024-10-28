<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Calendar;

use Pleb\VCardIO\Fields\AbstractField;

class CalUriField extends AbstractField
{
    protected string $name = 'caluri';

    protected bool $multiple = true;

    public function __construct(public string $uri, public array $attributes = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value, $attributes);
    }

    public function render(): mixed
    {
        return $this->uri;
    }

    public function __toString(): string
    {
        return $this->toString($this->uri, $this->attributes);
    }
}
