<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Explanatory;

use Pleb\VCardIO\Fields\AbstractField;

class SortStringField extends AbstractField
{
    protected string $name = 'sort-string';

    protected bool $multiple = false;

    public function __construct(public string $sortString) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value);
    }

    public function render(): mixed
    {
        return $this->sortString;
    }

    public function __toString(): string
    {
        return $this->toString($this->sortString);
    }
}
