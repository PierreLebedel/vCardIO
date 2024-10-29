<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\General;

use Pleb\VCardIO\Fields\AbstractField;

class SourceNameField extends AbstractField
{
    protected string $name = 'name';

    protected ?string $alias = 'sourceName';

    protected bool $multiple = true;

    public function __construct(public string $sourceName) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value);
    }

    public function render(): mixed
    {
        return $this->sourceName;
    }

    public function __toString(): string
    {
        return $this->toString($this->sourceName);
    }
}
