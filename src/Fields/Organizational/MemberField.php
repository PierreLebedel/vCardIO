<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Organizational;

use Pleb\VCardIO\Fields\AbstractField;

class MemberField extends AbstractField
{
    protected string $name = 'member';

    protected bool $multiple = true;

    public function __construct(public string $member, public array $attributes = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value, $attributes);
    }

    public function render(): mixed
    {
        return $this->member;
    }

    public function __toString(): string
    {
        return $this->toString($this->member, $this->attributes);
    }
}
