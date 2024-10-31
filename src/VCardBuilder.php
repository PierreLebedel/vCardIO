<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Pleb\VCardIO\Enums\VCardVersionEnum;
use Pleb\VCardIO\Exceptions\VCardBuilderException;
use Pleb\VCardIO\Fields\AgentField;
use Pleb\VCardIO\Fields\UriField;
use Pleb\VCardIO\Models\AbstractVCard;
use Ramsey\Uuid\Uuid;

class VCardBuilder
{
    public ?VCardVersionEnum $version = null;

    public array $fields = [];

    public array $properties = [];

    public function __construct() {}

    public static function make(): self
    {
        return new static;
    }

    public function getProperty(string $name): ?VCardProperty
    {
        if (substr(strtolower($name), 0, 2) == 'x-') {
            $name = 'x';
        }

        if (! array_key_exists($name, $this->properties)) {
            $property = VCardProperty::find($name);

            if (! $property) {
                return null;
            }

            $this->properties[$name] = $property;
        }

        return $this->properties[$name];
    }

    public function version(string|VCardVersionEnum $version): self
    {
        if ($version instanceof VCardVersionEnum) {
            $this->version = $version;
        } else {
            $versionEnum = VCardVersionEnum::tryFrom($version);
            if ($versionEnum) {
                $this->version = $versionEnum;
            }
        }

        return $this;
    }

    public function getVersion(): ?VCardVersionEnum
    {
        $versionEnum = null;

        $versionProperty = $this->getProperty('version');

        if ($versionProperty) {
            if (! empty($versionProperty->fields)) {
                $versionValue = reset($versionProperty->fields)->value;
                $versionEnum = VCardVersionEnum::tryFrom($versionValue);
            }
        }

        if (! $versionEnum) {
            $versionEnum = VCardVersionEnum::V40;
        }

        return $versionEnum;
    }

    public function agent(string|AbstractVCard $agent): self
    {
        $property = $this->getProperty('agent');

        if ($property) {
            if ($agent instanceof AbstractVCard) {
                $field = AgentField::makeFromVCard($agent);
            } else {
                $field = new UriField($agent);
            }
            $property->addField($field);
        }

        return $this;
    }

    public function fullName(string $fullName): self
    {
        $property = $this->getProperty('fn');
        if ($property) {
            $property->makeField($fullName);
        }

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

        $property = $this->getProperty('n');
        if ($property) {
            $property->makeField(implode(';', $nameParts));
        }

        return $this;
    }

    protected function namePart(int $index, string $namePart): self
    {
        $property = $this->getProperty('n');
        if ($property) {
            $field = (! empty($property->fields)) ? reset($property->fields) : $property->makeField('');
            $nameObject = $field->render();
            unset($nameObject->attributes);
            $nameParts = array_values((array) $nameObject);
            $nameParts[$index] = $namePart;
            $property->makeField(implode(';', $nameParts));
        }

        return $this;
    }

    public function lastName(string $lastName): self
    {
        return $this->namePart(0, $lastName);
    }

    public function firstName(string $firstName): self
    {
        return $this->namePart(1, $firstName);
    }

    public function middleName(string $middleName): self
    {
        return $this->namePart(2, $middleName);
    }

    public function namePrefix(string $namePrefix): self
    {
        return $this->namePart(3, $namePrefix);
    }

    public function nameSuffix(string $nameSuffix): self
    {
        return $this->namePart(4, $nameSuffix);
    }

    public function email(string $email, array $types = []): self
    {
        $property = $this->getProperty('email');
        if ($property) {
            $property->makeField($email, ['type' => $types]);
        }

        return $this;
    }

    public function phone(string $number, array $types = ['voice']): self
    {
        $property = $this->getProperty('tel');
        if ($property) {
            $property->makeField($number, ['type' => $types]);
        }

        return $this;
    }

    public function url(string $url): self
    {
        $property = $this->getProperty('url');
        if ($property) {
            $property->makeField($url);
        }

        return $this;
    }

    public function photo(string $photo): self
    {
        $property = $this->getProperty('photo');
        if ($property) {
            $property->makeField($photo);
        }

        return $this;
    }

    public function bday(DateTimeInterface $bday): self
    {
        $property = $this->getProperty('bday');
        if ($property) {
            $property->makeField($bday->format('Ymd'));
        }

        return $this;
    }

    public function birthday(DateTimeInterface $bday): self
    {
        return $this->bday($bday);
    }

    public function anniversary(DateTimeInterface $anniversary): self
    {
        $property = $this->getProperty('anniversary');
        if ($property) {
            $property->makeField($anniversary->format('Ymd'));
        }

        return $this;
    }

    public function kind(string $kind): self
    {
        $property = $this->getProperty('kind');
        if ($property) {
            $property->makeField($kind);
        }

        return $this;
    }

    public function gender(string $gender): self
    {
        if (! in_array(strtoupper($gender), ['M', 'F', 'O', 'N', 'U'])) {
            throw VCardBuilderException::wrongValue('gender', $gender);
        }

        $property = $this->getProperty('gender');
        if ($property) {
            $property->makeField($gender);
        }

        return $this;
    }

    public function organization(string $company, ?string $unit1 = null, ?string $unit2 = null): self
    {
        $property = $this->getProperty('org');
        if ($property) {
            $property->makeField(implode(';', [$company, $unit1, $unit2]));
        }

        return $this;
    }

    public function title(string $title): self
    {
        $property = $this->getProperty('title');
        if ($property) {
            $property->makeField($title);
        }

        return $this;
    }

    public function role(string $role): self
    {
        $property = $this->getProperty('role');
        if ($property) {
            $property->makeField($role);
        }

        return $this;
    }

    public function member(string $uid): self
    {
        $property = $this->getProperty('member');
        if ($property) {
            $property->makeField($uid);
        }

        return $this;
    }

    public function address(
        ?string $postOfficeAddress = null,
        ?string $extendedAddress = null,
        ?string $street = null,
        ?string $locality = null,
        ?string $region = null,
        ?string $postalCode = null,
        ?string $country = null,
        array $types = []
    ): self {
        $property = $this->getProperty('org');
        if ($property) {
            $property->makeField(implode(';', [
                $postOfficeAddress,
                $extendedAddress,
                $street,
                $locality,
                $region,
                $postalCode,
                $country,
            ]), ['type' => $types]);
        }

        return $this;
    }

    public function geo(float $latitude, float $longitude): self
    {
        $property = $this->getProperty('geo');
        if ($property) {
            $property->makeField(implode(',', [$latitude, $longitude]));
        }

        return $this;
    }

    public function categories(array $categories): self
    {
        $property = $this->getProperty('categories');
        if ($property) {
            $property->makeField(implode(',', $categories));
        }

        return $this;
    }

    public function category(string $category): self
    {
        $property = $this->getProperty('categories');
        if ($property) {
            $field = (! empty($property->fields)) ? reset($property->fields) : $property->makeField('');
            $categoriesArray = $field->render()->value;
            if (! in_array($category, $categoriesArray)) {
                $categoriesArray[] = $category;
            }
            $property->fields = [];
            $property->makeField(implode(',', $categoriesArray));
        }

        return $this;
    }

    public function nickNames(array $nickNames): self
    {
        $property = $this->getProperty('nickname');
        if ($property) {
            $property->makeField(implode(',', $nickNames));
        }

        return $this;
    }

    public function nickName(string $nickName): self
    {
        $property = $this->getProperty('nickname');
        if ($property) {
            $field = (! empty($property->fields)) ? reset($property->fields) : $property->makeField('');
            $nickNamesArray = $field->render()->value;
            if (! in_array($nickName, $nickNamesArray)) {
                $nickNamesArray[] = $nickName;
            }
            $property->fields = [];
            $property->makeField(implode(',', $nickNamesArray));
        }

        return $this;
    }

    public function timeZone(DateTimeZone $timeZone): self
    {
        $property = $this->getProperty('tz');
        if ($property) {
            $property->makeField($timeZone->getName());
        }

        return $this;
    }

    public function uid(string $uid): self
    {
        $property = $this->getProperty('uid');
        if ($property) {
            $property->makeField($uid);
        }

        return $this;
    }

    public function uuid(string $uuid): self
    {
        return $this->uid($uuid);
    }

    public function calendarAddressUri(string $uri): self
    {
        $property = $this->getProperty('caladruri');
        if ($property) {
            $property->makeField($uri);
        }

        return $this;
    }

    public function calendarUri(string $uri): self
    {
        $property = $this->getProperty('caluri');
        if ($property) {
            $property->makeField($uri);
        }

        return $this;
    }

    public function clientPidMap(int $pid, string $uri): self
    {
        $property = $this->getProperty('clientpidmap');
        if ($property) {
            $property->makeField(implode(',', [$pid, $uri]));
        }

        return $this;
    }

    public function fbUrl(string $url): self
    {
        $property = $this->getProperty('fburl');
        if ($property) {
            $property->makeField($url);
        }

        return $this;
    }

    public function impp(string $number, array $types = []): self
    {
        $property = $this->getProperty('impp');
        if ($property) {
            $property->makeField($number, ['type' => $types]);
        }

        return $this;
    }

    public function key(string $key): self
    {
        $property = $this->getProperty('key');
        if ($property) {
            $property->makeField($key);
        }

        return $this;
    }

    public function lang(string $lang, int $pref = 1): self
    {
        $property = $this->getProperty('lang');
        if ($property) {
            $property->makeField($lang, ['pref' => $pref]);
        }

        return $this;
    }

    public function langs(array $langs): self
    {
        foreach ($langs as $k => $lang) {
            $this->lang($lang, ($k + 1));
        }

        return $this;
    }

    public function logo(string $logo): self
    {
        $property = $this->getProperty('logo');
        if ($property) {
            $property->makeField($logo);
        }

        return $this;
    }

    public function note(string $note): self
    {
        $property = $this->getProperty('note');
        if ($property) {
            $property->makeField($note);
        }

        return $this;
    }

    public function prodid(string $prodid): self
    {
        $property = $this->getProperty('prodid');
        if ($property) {
            $property->makeField($prodid);
        }

        return $this;
    }

    public function related(string $related): self
    {
        $property = $this->getProperty('related');
        if ($property) {
            $property->makeField($related);
        }

        return $this;
    }

    public function rev(DateTimeInterface $dateTime): self
    {
        $property = $this->getProperty('rev');
        if ($property) {
            $property->makeField($dateTime->format('Ymd\THis\Z'));
        }

        return $this;
    }

    public function revision(DateTimeInterface $dateTime): self
    {

        return $this->rev($dateTime);
    }

    public function sound(string $sound): self
    {
        $property = $this->getProperty('sound');
        if ($property) {
            $property->makeField($sound);
        }

        return $this;
    }

    public function source(string $source): self
    {
        $property = $this->getProperty('source');
        if ($property) {
            $property->makeField($source);
        }

        return $this;
    }

    public function xml(string $xml): self
    {
        $property = $this->getProperty('xml');
        if ($property) {
            $property->makeField($xml);
        }

        return $this;
    }

    public function x(string $name, string $value): self
    {
        $property = $this->getProperty('x');
        if ($property) {
            $property->makeXField($name, $value);
        }

        return $this;
    }

    public function get(): AbstractVCard
    {
        $vCardClass = $this->getVersion()->getVCardClass();

        $vCard = new $vCardClass;

        foreach ($this->properties as $property) {
            $vCard->applyProperty($property);
        }

        if (! $vCard->getRev()) {
            $property = $this->getProperty('rev');
            if ($property) {
                $property->makeField((new DateTimeImmutable('now'))->format('Ymd\THis\Z'));
                $vCard->applyProperty($property);
            }
        }

        if (! $vCard->getProdid()) {
            $property = $this->getProperty('prodid');
            if ($property) {
                $property->makeField('-//Pleb//Pleb vCardIO '.VCardPackage::VERSION.' //EN');
                $vCard->applyProperty($property);
            }
        }

        if (! $vCard->getUid()) {
            $property = $this->getProperty('uid');
            if ($property) {
                $uuid4 = Uuid::uuid4();
                $property->makeField('urn:uuid:'.$uuid4->toString());
                $vCard->applyProperty($property);
            }
        }

        return $vCard;
    }

    public function __toString(): string
    {
        return (string) $this->get();
    }
}
