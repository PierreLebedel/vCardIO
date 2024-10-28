<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Organizational;

use Pleb\VCardIO\Fields\AbstractField;

class TitleField extends AbstractField
{
    protected string $name = 'title';

    protected bool $multiple = true;

    public function __construct(public string $title, public array $attributes = []) {}

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
            'altid',
        ];
    }

    public function render(): mixed
    {
        return $this->title;
    }

    public function __toString(): string
    {
        return $this->toString($this->title, $this->attributes);
    }
}
