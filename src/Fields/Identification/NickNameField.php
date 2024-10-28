<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Identification;

use Pleb\VCardIO\Fields\AbstractField;

class NickNameField extends AbstractField
{
    protected string $name = 'nickname';

    protected ?string $alias = 'nickNames';

    protected bool $multiple = true;

    public function __construct(public array $nickNames, public array $attributes = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        $nickNames = explode(',', $value);

        return new self($nickNames, $attributes);
    }

    public static function getDefaultValue(): mixed
    {
        return [];
    }

    public static function getPossibleAttributes(): array
    {
        return [
            'type',
            'language',
            'altid',
            'pid',
            'pref',
        ];
    }

    public function render(): mixed
    {
        return $this->nickNames;
    }

    public function __toString(): string
    {

        return $this->toString(implode(',', $this->nickNames), $this->attributes);
    }
}
