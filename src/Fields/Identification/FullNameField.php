<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Identification;

use Pleb\VCardIO\Fields\AbstractField;

class FullNameField extends AbstractField
{
    protected string $name = 'fn';

    protected ?string $alias = 'fullName';

    protected bool $multiple = false;

    public function __construct(public string $fullName) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value);
    }

    public function render(): mixed
    {
        return $this->fullName;
    }

    public function __toString(): string
    {
        return $this->toString($this->fullName);
    }
}
