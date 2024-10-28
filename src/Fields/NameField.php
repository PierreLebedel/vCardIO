<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields;

class NameField extends AbstractField
{
    protected string $name = 'n';

    protected ?string $alias = 'name';

    protected bool $multiple = false;

    public function __construct(public array $nameParts) {}

    public static function make(string $value, array $attributes = []): self
    {
        $names = explode(',', $value)[0] ?? ';;;;';
        return new self(explode(';', $names, 5));
    }

    public static function getDefaultValue(): mixed
    {
        return [null, null, null, null, null];
    }

    public function render(): mixed
    {
        return (object) [
            'lastName'   => $this->nameParts[0] ?? '',
            'firstName'  => $this->nameParts[1] ?? '',
            'middleName' => $this->nameParts[2] ?? '',
            'namePrefix' => $this->nameParts[3] ?? '',
            'nameSuffix' => $this->nameParts[4] ?? '',
        ];
    }

    public function __toString(): string
    {
        return $this->toString(implode(';', array_values($this->nameParts)));
    }
}
