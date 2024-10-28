<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Calendar;

use Pleb\VCardIO\Fields\AbstractField;

class CalAdrUriField extends AbstractField
{
    protected string $name = 'caladruri';

    protected ?string $alias = 'calendarUserUri';

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
