<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Identification;

use Pleb\VCardIO\Fields\AbstractField;

class NameField extends AbstractField
{
    protected string $name = 'n';

    protected ?string $alias = 'name';

    protected bool $multiple = false;

    public function __construct(public array $nameParts) {}

    public static function make(string $value, array $attributes = []): self
    {
        $parts = explode(',', $value)[0] ?? ';;;;';

        return new self(explode(';', $parts, 5));
    }

    public static function getDefaultValue(): mixed
    {
        return [null, null, null, null, null];
    }

    public static function getPossibleAttributes(): array
    {
        return [
            'sort-as',
            'language',
            'altid',
        ];
    }

    public function render(): mixed
    {
        return (object) [
            'lastName'   => $this->nameParts[0] ?? null,
            'firstName'  => $this->nameParts[1] ?? null,
            'middleName' => $this->nameParts[2] ?? null,
            'namePrefix' => $this->nameParts[3] ?? null,
            'nameSuffix' => $this->nameParts[4] ?? null,
        ];
    }

    public function __toString(): string
    {
        return $this->toString(implode(';', array_values($this->nameParts)));
    }
}
