<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use DateTime;
use DateTimeInterface;
use Pleb\VCardIO\Enums\VCardVersionEnum;
use Pleb\VCardIO\Exceptions\VCardBuilderException;
use Pleb\VCardIO\Fields\AbstractField;
use Pleb\VCardIO\Fields\Emailfield;
use Pleb\VCardIO\Fields\FullNameField;
use Pleb\VCardIO\Fields\NameField;
use Pleb\VCardIO\Models\AbstractVCard;

class VCardBuilder
{
    public VCardVersionEnum $version;

    public array $fields = [];

    public function __construct(?VCardVersionEnum $version = null)
    {
        $this->setVersion($version ?? VCardVersionEnum::V40);

        // $this->vCard->PRODID = '-//Pleb vCardIO';
        // $this->vCard->REV = (new DateTime('now'))->format('Ymd\THis\Z');
    }

    public static function make(?VCardVersionEnum $version = null): self
    {
        return new static($version);
    }

    public function setVersion(VCardVersionEnum $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getVersion(): ?VCardVersionEnum
    {
        return $this->version;
    }

    // public function setAgent(VCard $agent) :self
    // {
    //     $this->vCardAgent = $agent;
    //     return $this;
    // }

    public function addField(AbstractField $field): self
    {
        if (! array_key_exists($field->getName(), $this->fields)) {
            $this->fields[$field->getName()] = [];
        }

        if (! $field->isMultiple()) {
            $this->fields[$field->getName()][0] = $field;
        } else {
            $this->fields[$field->getName()][] = $field;
        }

        return $this;
    }

    public function fullName(?string $fullName): self
    {
        $this->addField(new FullNameField($fullName));

        return $this;
    }

    public function name(
        ?string $lastName = null,
        ?string $firstName = null,
        ?string $middleName = null,
        ?string $namePrefix = null,
        ?string $nameSuffix = null
    ): self {

        $nameParts = [
            $lastName,
            $firstName,
            $middleName,
            $namePrefix,
            $nameSuffix,
        ];

        $this->addField(new NameField($nameParts));

        return $this;
    }

    protected function namePart(int $index, string $namePart): self
    {
        if (! $fieldClass = VCardParser::fieldsMap()['n']) {
            return $this;
        }
        $currentNameField = (array_key_exists('n', $this->fields) && count($this->fields['n']) == 1)
            ? array_values((array) $this->fields['n'][0]->render())
            : $fieldClass::getDefaultValue();
        $currentNameField[$index] = $namePart;

        $this->addField(new NameField($currentNameField));

        return $this;
    }

    public function lastName(?string $lastName): self
    {
        return $this->namePart(0, $lastName);
    }

    public function firstName(?string $firstName): self
    {
        return $this->namePart(1, $firstName);
    }

    public function middleName(?string $middleName): self
    {
        return $this->namePart(2, $middleName);
    }

    public function namePrefix(?string $namePrefix): self
    {
        return $this->namePart(3, $namePrefix);
    }

    public function nameSuffix(?string $nameSuffix): self
    {
        return $this->namePart(4, $nameSuffix);
    }

    public function email(string $email, array $types = []): self
    {
        $this->addField(new Emailfield($email, $types));

        return $this;
    }

    public function tel(string $number, array $attributes = []): self
    {
        //$this->property('tel', $number, $attributes);

        return $this;
    }

    public function url(string $url): self
    {
        //$this->property('url', $url);

        return $this;
    }

    public function photo(string $photo): self
    {
        //$this->property('photo', $photo);

        return $this;
    }

    public function bday(DateTimeInterface $bday): self
    {
        //$this->property('bday', $bday->format('Y-m-d'));

        return $this;
    }

    public function anniversary(DateTimeInterface $anniversary): self
    {
        //$this->property('anniversary', $anniversary->format('Y-m-d'));

        return $this;
    }

    public function kind(string $kind): self
    {
        if (! in_array(strtolower($kind), ['individual', 'group', 'org', 'location'])) {
            throw VCardBuilderException::wrongStringValue('kind', $kind);
        }

        //$this->property('kind', strtolower($kind));
        return $this;
    }

    public function gender(string $gender): self
    {
        if (! in_array(strtolower($gender), ['f', 'm', 'o', 'n', 'u'])) {
            throw VCardBuilderException::wrongStringValue('gender', $gender);
        }

        //$this->property('gender', strtolower($gender));
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

    public function get(): AbstractVCard
    {
        $vCardClass = $this->version->getVCardClass();

        $vCard = new $vCardClass;

        foreach ($this->fields as $name => $fields) {
            foreach ($fields as $field) {
                $vCard->applyField($field);
            }
        }

        return $vCard;
    }

    public function __toString(): string
    {
        return (string) $this->get();
    }
}
