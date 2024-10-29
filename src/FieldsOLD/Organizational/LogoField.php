<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Organizational;

use Pleb\VCardIO\Fields\AbstractField;

class LogoField extends AbstractField
{
    protected string $name = 'logo';

    protected bool $multiple = true;

    public function __construct(public string $logo, public array $attributes = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value, $attributes);
    }

    public static function getPossibleAttributes(): array
    {
        return [
            'language',
            'pid',
            'pref',
            'type',
            'mediatype',
            'altid',
        ];
    }

    public function render(): mixed
    {
        return $this->logo;
    }

    public function __toString(): string
    {
        return $this->toString($this->logo, $this->attributes);
    }
}
