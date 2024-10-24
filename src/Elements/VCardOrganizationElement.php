<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Elements;

class VCardOrganizationElement extends VCardStructuredElement
{
    public ?string $name = null;

    public ?string $units1 = null;

    public ?string $units2 = null;

    public function definition(): array
    {
        return [
            'name',
            'units1',
            'units2',
        ];
    }
}
