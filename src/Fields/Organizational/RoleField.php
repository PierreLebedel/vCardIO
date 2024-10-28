<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Organizational;

use Pleb\VCardIO\Fields\AbstractField;

class RoleField extends AbstractField
{
    protected string $name = 'role';

    protected bool $multiple = true;

    public function __construct(public string $role, public array $attributes = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value, $attributes);
    }

    public function render(): mixed
    {
        return $this->role;
    }

    public function __toString(): string
    {
        return $this->toString($this->role, $this->attributes);
    }
}
