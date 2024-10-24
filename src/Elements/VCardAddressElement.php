<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Elements;

class VCardAddressElement extends VCardStructuredElement
{
    public ?string $postOfficeAddress = null;

    public ?string $extendedAddress = null;

    public ?string $street = null;

    public ?string $locality = null;

    public ?string $region = null;

    public ?string $postalCode = null;

    public ?string $country = null;

    public ?array $restrictedTypes = ['dom', 'intl', 'postal', 'parcel', 'home', 'work', 'pref'];

    public function isMultiple(): bool
    {
        return true;
    }

    public function definition(): array
    {
        return [
            'postOfficeAddress',
            'extendedAddress',
            'street',
            'locality',
            'region',
            'postalCode',
            'country',
        ];
    }
}
