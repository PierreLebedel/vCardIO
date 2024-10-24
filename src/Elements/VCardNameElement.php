<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Elements;

class VCardNameElement extends VCardStructuredElement
{
    public ?string $lastName = null;

    public ?string $firstName = null;

    public ?string $middleName = null;

    public ?string $namePrefix = null;

    public ?string $nameSuffix = null;

    public function definition(): array
    {
        return [
            'lastName',
            'firstName',
            'middleName',
            'namePrefix',
            'nameSuffix',
        ];
    }
}
