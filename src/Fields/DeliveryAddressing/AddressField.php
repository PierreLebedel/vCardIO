<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\DeliveryAddressing;

use Pleb\VCardIO\Fields\AbstractField;

class AddressField extends AbstractField
{
    protected string $name = 'adr';

    protected ?string $alias = 'addresses';

    protected bool $multiple = true;

    // 'type', ['dom', 'intl', 'postal', 'parcel', 'home', 'work', 'pref']

    public function __construct(public array $addressParts) {}

    public static function make(string $value, array $attributes = []): self
    {
        $parts = explode(',', $value)[0] ?? ';;;;;;';

        return new self(explode(';', $parts, 7));
    }

    public static function getDefaultValue(): mixed
    {
        return [null, null, null, null, null, null, null];
    }

    public function render(): mixed
    {
        return (object) [
            'postOfficeAddress' => $this->addressParts[0] ?? null,
            'extendedAddress'   => $this->addressParts[1] ?? null,
            'street'            => $this->addressParts[2] ?? null,
            'locality'          => $this->addressParts[3] ?? null,
            'region'            => $this->addressParts[4] ?? null,
            'postalCode'        => $this->addressParts[5] ?? null,
            'country'           => $this->addressParts[6] ?? null,
        ];
    }

    public function __toString(): string
    {
        return $this->toString(implode(';', array_values($this->addressParts)));
    }
}
