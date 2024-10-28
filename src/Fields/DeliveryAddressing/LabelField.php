<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\DeliveryAddressing;

use Pleb\VCardIO\Fields\AbstractField;

class LabelField extends AbstractField
{
    protected string $name = 'label';

    protected ?string $alias = 'labels';

    protected bool $multiple = true;

    // 'type', ['dom', 'intl', 'postal', 'parcel', 'home', 'work', 'pref']

    public function __construct(public array $labelParts) {}

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
            'postOfficeAddress' => $this->labelParts[0] ?? null,
            'extendedAddress'   => $this->labelParts[1] ?? null,
            'street'            => $this->labelParts[2] ?? null,
            'locality'          => $this->labelParts[3] ?? null,
            'region'            => $this->labelParts[4] ?? null,
            'postalCode'        => $this->labelParts[5] ?? null,
            'country'           => $this->labelParts[6] ?? null,
        ];
    }

    public function __toString(): string
    {
        return $this->toString(implode(';', array_values($this->labelParts)));
    }
}
