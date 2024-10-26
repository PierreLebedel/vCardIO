<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use DateTime;
use DateTimeInterface;
use Pleb\VCardIO\Enums\VCardVersionEnum;
use Pleb\VCardIO\Exceptions\VCardBuilderException;
use stdClass;

class VCardBuilder
{
    public VCardVersionEnum $version;

    public array $properties = [];

    public function __construct(?VCardVersionEnum $version = null)
    {
        $this->version = $version ?? VCardVersionEnum::V40;
    }

    public static function make(?VCardVersionEnum $version = null): self
    {
        return new static($version);
    }

    protected function property(string $name, string $value, array $attributes = []): self
    {
        if (! array_key_exists($name, $this->version->getDataFields())) {
            dd('no property: '.$name);
        }

        $formattedAttributes = [];
        foreach ($attributes as $k => $v) {
            $formattedAttributes[] = strtoupper($k).'='.(is_array($v) ? implode(',', $v) : $v);
        }

        $property = implode(';', array_merge([strtoupper($name)], $formattedAttributes));
        $property .= ':'.$value;

        if (in_array($name, VCard::getSingularFields())) {
            $this->properties[$name] = $property;
        } else {
            $this->properties[$name][] = $property;
        }

        return $this;
    }

    // protected function property(string $property, mixed $formattedValue, mixed $rawValue = null): self
    // {
    //     if (! $rawValue) {
    //         $rawValue = $formattedValue;
    //     }

    //     @[$property, $subProperty] = explode(':', $property, 2);

    //     if (! array_key_exists($property, $this->vCard->getDataFields())) {
    //         $this->vCard->invalidData->{$property} = $rawValue;

    //         return $this;
    //     }

    //     $alias = $this->vCard->getDataFields()[$property];

    //     if ($subProperty) {
    //         if (! in_array($property, VCard::getSingularFields())) {
    //             throw VCardBuilderException::notSingularField($property);
    //         }
    //         if (! $this->vCard->formattedData->{$alias}) {
    //             $this->vCard->formattedData->{$alias} = new stdClass;
    //         }
    //         $this->vCard->formattedData->{$alias}->{$subProperty} = $formattedValue;

    //     } else {
    //         if (in_array($property, VCard::getSingularFields())) {
    //             $this->vCard->formattedData->{$alias} = $formattedValue;
    //             $this->vCard->rawData->{$property} = $rawValue;

    //         } else {
    //             if (! is_array($this->vCard->formattedData->{$alias})) {
    //                 $this->vCard->formattedData->{$alias} = [];
    //             }
    //             $this->vCard->formattedData->{$alias}[] = $formattedValue;

    //             if (! is_array($this->vCard->rawData->{$property})) {
    //                 $this->vCard->rawData->{$property} = [];
    //             }
    //             $this->vCard->rawData->{$property}[] = $rawValue;
    //         }

    //     }

    //     return $this;
    // }

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

        $formattedValue = [
            $lastName,
            $firstName,
            $middleName,
            $namePrefix,
            $nameSuffix,
        ];

        $this->property('n', implode(';', $formattedValue));

        return $this;
    }

    public function lastName(?string $lastName): self
    {
        $names = $this->properties['N'] ?? ';;;;';
        $namesArray = explode(';', $names, 5);
        $namesArray[0] = $lastName;
        $this->property('n', implode(';', $namesArray));

        return $this;
    }

    public function firstName(?string $firstName): self
    {
        $names = $this->properties['n'] ?? ';;;;';
        $namesArray = explode(';', $names, 5);
        $namesArray[1] = $firstName;
        $this->property('n', implode(';', $namesArray));

        return $this;
    }

    public function middleName(?string $middleName): self
    {
        $names = $this->properties['n'] ?? ';;;;';
        $namesArray = explode(';', $names, 5);
        $namesArray[2] = $middleName;
        $this->property('n', implode(';', $namesArray));

        return $this;
    }

    public function namePrefix(?string $namePrefix): self
    {
        $names = $this->properties['n'] ?? ';;;;';
        $namesArray = explode(';', $names, 5);
        $namesArray[3] = $namePrefix;
        $this->property('n', implode(';', $namesArray));

        return $this;
    }

    public function nameSuffix(?string $nameSuffix): self
    {
        $names = $this->properties['n'] ?? ';;;;';
        $namesArray = explode(';', $names, 5);
        $namesArray[4] = $nameSuffix;
        $this->property('n', implode(';', $namesArray));

        return $this;
    }

    public function email(string $email, array $attributes = []): self
    {
        $this->property('email', $email, $attributes);

        return $this;
    }

    public function tel(string $number, array $attributes = []): self
    {
        $this->property('tel', $number, $attributes);

        return $this;
    }

    public function url(string $url): self
    {
        $this->property('url', $url);

        return $this;
    }

    public function photo(string $photo): self
    {
        $this->property('photo', $photo);

        return $this;
    }

    public function bday(DateTimeInterface $bday): self
    {
        $this->property('bday', $bday->format('Y-m-d'));

        return $this;
    }

    public function anniversary(DateTimeInterface $anniversary): self
    {
        $this->property('anniversary', $anniversary->format('Y-m-d'));

        return $this;
    }

    public function kind(string $kind): self
    {
        if (! in_array(strtolower($kind), ['individual', 'group', 'org', 'location'])) {
            throw VCardBuilderException::wrongStringValue('kind', $kind);
        }
        $this->property('kind', strtolower($kind));

        return $this;
    }

    public function gender(string $gender): self
    {
        if (! in_array(strtolower($gender), ['f', 'm', 'o', 'n', 'u'])) {
            throw VCardBuilderException::wrongStringValue('gender', $gender);
        }
        $this->property('gender', strtolower($gender));

        return $this;
    }

    // public function org(?string $company = null, ?string $unit = null, ?string $team = null): self
    // {
    //     $this->properties[] = new Org($company, $unit, $team);

    //     return $this;
    // }

    // public function title(string $title): self
    // {
    //     $this->properties[] = new Title($title);

    //     return $this;
    // }

    // public function role(string $role): self
    // {
    //     $this->properties[] = new Role($role);

    //     return $this;
    // }

    // public function member(?string $mail = null, ?string $uuid = null): self
    // {
    //     $this->properties[] = new Member($mail, $uuid);

    //     return $this;
    // }

    // public function adr(
    //     ?string $poBox = null,
    //     ?string $extendedAddress = null,
    //     ?string $streetAddress = null,
    //     ?string $locality = null,
    //     ?string $region = null,
    //     ?string $postalCode = null,
    //     ?string $countryName = null,
    //     array $types = [Adr::WORK]
    // ): self {
    //     $this->properties[] = new Adr(
    //         $poBox,
    //         $extendedAddress,
    //         $streetAddress,
    //         $locality,
    //         $region,
    //         $postalCode,
    //         $countryName,
    //         $types
    //     );

    //     return $this;
    // }

    // public function note(string $note): self
    // {
    //     $this->properties[] = new Note($note);

    //     return $this;
    // }

    // public function source(string $source): self
    // {
    //     $this->properties[] = new Source($source);

    //     return $this;
    // }

    public function get(): VCard
    {
        $collection = VCardParser::parseRaw((string) $this);

        return $collection->getVCard(0);
    }

    public function __toString(): string
    {
        $propertiesArray = [];
        foreach ([
            'BEGIN:VCARD',
            'VERSION:'.$this->version->value,
        ] as $property) {
            $propertiesArray[] = $property;
        }

        foreach ($this->properties as $name => $values) {
            if (is_array($values)) {
                foreach ($values as $value) {
                    $propertiesArray[] = $value;
                }
            } else {
                $propertiesArray[] = $values;
            }
        }

        foreach ([
            'PRODID:-//Pleb vCardIO',
            'REV:'.(new DateTime('now'))->format('Ymd\THis\Z'),
            'END:VCARD',
        ] as $property) {
            $propertiesArray[] = $property;
        }

        return implode(PHP_EOL, $propertiesArray);
    }
}
