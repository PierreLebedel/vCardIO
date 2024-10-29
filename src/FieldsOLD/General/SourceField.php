<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\General;

use Pleb\VCardIO\Fields\AbstractField;

class SourceField extends AbstractField
{
    protected string $name = 'source';

    protected ?string $alias = 'sources';

    protected bool $multiple = true;

    public function __construct(public string $source, public array $attributes = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value);
    }

    public function render(): mixed
    {
        return $this->source;
    }

    public function __toString(): string
    {
        return $this->toString($this->source, $this->attributes);
    }
}
