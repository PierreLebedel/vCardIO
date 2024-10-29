<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Organizational;

use Pleb\VCardIO\Fields\AbstractField;

class RelatedField extends AbstractField
{
    protected string $name = 'related';

    protected bool $multiple = true;

    public function __construct(public string $related, public array $attributes = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value, $attributes);
    }

    public function render(): mixed
    {
        return $this->related;
    }

    public function __toString(): string
    {
        return $this->toString($this->related, $this->attributes);
    }
}
