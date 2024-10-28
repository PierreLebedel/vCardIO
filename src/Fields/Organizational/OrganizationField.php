<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Organizational;

use Pleb\VCardIO\Fields\AbstractField;

class OrganizationField extends AbstractField
{
    protected string $name = 'org';

    protected ?string $alias = 'organizations';

    protected bool $multiple = true;

    public function __construct(public array $orgParts) {}

    public static function make(string $value, array $attributes = []): self
    {
        $parts = explode(',', $value)[0] ?? ';;';

        return new self(explode(';', $parts, 3));
    }

    public static function getDefaultValue(): mixed
    {
        return [null, null, null];
    }

    public function render(): mixed
    {
        return (object) [
            'name'  => $this->orgParts[0] ?? null,
            'unit1' => $this->orgParts[1] ?? null,
            'unit2' => $this->orgParts[2] ?? null,
        ];
    }

    public function __toString(): string
    {
        return $this->toString(implode(';', array_values($this->orgParts)));
    }
}
