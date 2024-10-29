<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Security;

use Pleb\VCardIO\Fields\AbstractField;

class KeyField extends AbstractField
{
    protected string $name = 'key';

    protected bool $multiple = true;

    public function __construct(public string $key, public array $attributes = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value, $attributes);
    }

    public function render(): mixed
    {
        return $this->key;
    }

    public function __toString(): string
    {
        return $this->toString($this->key, $this->attributes);
    }
}
