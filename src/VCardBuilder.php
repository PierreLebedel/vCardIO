<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use DateTime;
use Pleb\VCardIO\Enums\VCardVersionEnum;
use Pleb\VCardIO\Exceptions\VCardBuilderException;
use stdClass;

class VCardBuilder
{
    public VCard $vCard;

    public function __construct(?VCardVersionEnum $version = null)
    {
        $this->vCard = new VCard;

        if (! $version) {
            $version = VCardVersionEnum::V40;
        }
        $this->vCard->setVersion($version);

        $now = new DateTime('now');
        $this->property('rev', $now, $now->format('Ymd\THis\Z'));
    }

    public static function make(?VCardVersionEnum $version = null): self
    {
        return new static($version);
    }

    protected function property(string $property, mixed $formattedValue, mixed $rawValue = null): self
    {
        if (! $rawValue) {
            $rawValue = $formattedValue;
        }

        @[$property, $subProperty] = explode(':', $property, 2);

        if (! array_key_exists($property, $this->vCard->getDataFields())) {
            $this->vCard->invalidData->{$property} = $rawValue;

            return $this;
        }

        $alias = $this->vCard->getDataFields()[$property];

        if ($subProperty) {
            if (! in_array($property, VCard::getSingularFields())) {
                throw VCardBuilderException::notSingularField($property);
            }
            if (! $this->vCard->formattedData->{$alias}) {
                $this->vCard->formattedData->{$alias} = new stdClass;
            }
            $this->vCard->formattedData->{$alias}->{$subProperty} = $formattedValue;

        } else {
            if (in_array($property, VCard::getSingularFields())) {
                $this->vCard->formattedData->{$alias} = $formattedValue;
                $this->vCard->rawData->{$property} = $rawValue;

            } else {
                if (! is_array($this->vCard->formattedData->{$alias})) {
                    $this->vCard->formattedData->{$alias} = [];
                }
                $this->vCard->formattedData->{$alias}[] = $formattedValue;

                if (! is_array($this->vCard->rawData->{$property})) {
                    $this->vCard->rawData->{$property} = [];
                }
                $this->vCard->rawData->{$property}[] = $rawValue;
            }

        }

        return $this;
    }

    public function fullName(?string $fullName): self
    {
        $this->property('fn', $fullName);

        return $this;
    }

    public function name(
        ?string $lastName = null,
        ?string $firstName = null,
        ?string $middleName = null,
        ?string $namePrefix = null,
        ?string $nameSuffix = null
    ): self {

        $formattedValue = (object) [
            'lastName'   => $lastName,
            'firstName'  => $firstName,
            'middleName' => $middleName,
            'namePrefix' => $namePrefix,
            'nameSuffix' => $nameSuffix,
        ];

        $this->property('fn', $formattedValue, array_values((array) $formattedValue));

        return $this;
    }

    public function lastName(?string $lastName): self
    {
        $this->property('n:lastName', $lastName);

        return $this;
    }

    public function firstName(?string $firstName): self
    {
        $this->property('n:firstName', $firstName);

        return $this;
    }

    public function middleName(?string $middleName): self
    {
        $this->property('n:middleName', $middleName);

        return $this;
    }

    public function namePrefix(?string $namePrefix): self
    {
        $this->property('n:namePrefix', $namePrefix);

        return $this;
    }

    public function nameSuffix(?string $nameSuffix): self
    {
        $this->property('n:nameSuffix', $nameSuffix);

        return $this;
    }

    // protected function setRawValues(): self
    // {
    //     return $this;
    // }

    public function get(): VCard
    {
        return $this->vCard;
    }

    public function __toString(): string
    {
        return (string) $this->get();
    }
}
