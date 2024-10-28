<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use Sabre\VObject;
use Sabre\VObject\Reader;

class VCardBuilder
{
    //public ?VCardVersionEnum $version = null;

    //public array $fields = [];

    public string $rawData = '';

    public function __construct() {}

    public static function make(): self
    {
        return new static;
    }

    public function addLine(string $lineRawData)
    {
        if(!empty($this->rawData)){
            $this->rawData .= PHP_EOL;
        }
        $this->rawData .= $lineRawData;
    }

    /*public function setVersion(VCardVersionEnum $version): self
    {
        $this->version = $version;

        return $this;
    }

    public function getVersion(): ?VCardVersionEnum
    {
        return $this->version;
    }

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

    public function agent(string|AbstractVCard $agent): self
    {
        $this->addField(new AgentField($agent));

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
        $this->addField(new EmailField($email, $types));

        return $this;
    }

    public function phone(string $number, array $types = []): self
    {
        $this->addField(new PhoneField($number, $types));

        return $this;
    }

    public function url(string $url): self
    {
        $this->addField(new UrlField($url));

        return $this;
    }

    public function photo(string $photo): self
    {
        $this->addField(new PhotoField($photo));

        return $this;
    }

    public function birthday(DateTimeInterface $bday): self
    {
        $this->addField(new BirthdayField($bday));

        return $this;
    }

    public function anniversary(DateTimeInterface $anniversary): self
    {
        $this->addField(new AnniversaryField($anniversary));

        return $this;
    }

    public function kind(string $kind): self
    {
        $this->addField(KindField::make($kind));

        return $this;
    }

    public function gender(string $gender): self
    {
        $this->addField(GenderField::make($gender));

        return $this;
    }

    public function organization(?string $company = null, ?string $unit1 = null, ?string $unit2 = null): self
    {
        $this->addField(new OrganizationField([$company, $unit1, $unit2]));

        return $this;
    }

    public function title(string $title): self
    {
        $this->addField(new TitleField($title));

        return $this;
    }

    public function role(string $role): self
    {
        $this->addField(new RoleField($role));

        return $this;
    }

    public function member(string $uid): self
    {
        $this->addField(new MemberField($uid));

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
        $this->addField(new AddressField([
            'postOfficeAddress' => $postOfficeAddress,
            'extendedAddress'   => $extendedAddress,
            'street'            => $street,
            'locality'          => $locality,
            'region'            => $region,
            'postalCode'        => $postalCode,
            'country'           => $country,
        ], $types));

        return $this;
    }

    public function geoLocation(float $latitude, float $longitude): self
    {
        $this->addField(new GeoField($latitude, $longitude));

        return $this;
    }

    public function categories(array $categories): self
    {
        $this->addField(new CategoriesField($categories));

        return $this;
    }

    public function category(string $category): self
    {
        if (! $fieldClass = VCardParser::fieldsMap()['categories']) {
            return $this;
        }
        $currentCategoriesField = (array_key_exists('categories', $this->fields) && count($this->fields['categories']) == 1)
            ? array_values((array) $this->fields['categories'][0]->render())
            : $fieldClass::getDefaultValue();

            dump($currentCategoriesField, $category);

        if (! in_array($category, $currentCategoriesField)) {
            $currentCategoriesField[] = $category;
        }

        // @todo
        //$this->addField(new CategoriesField($currentCategoriesField));

        return $this;
    }

    public function nickNames(array $nicknames): self
    {
        $this->addField(new NickNameField($nicknames));

        return $this;
    }

    public function nickName(string $nickName): self
    {
        if (! $fieldClass = VCardParser::fieldsMap()['nickname']) {
            return $this;
        }
        $currentNickNamesField = (array_key_exists('nickname', $this->fields) && count($this->fields['nickname']) == 1)
            ? array_values((array) $this->fields['nickname'][0]->render())
            : $fieldClass::getDefaultValue();

        if (! in_array($nickName, $currentNickNamesField)) {
            $currentNickNamesField[] = $nickName;
        }

        $this->addField(new NickNameField($currentNickNamesField));

        return $this;
    }

    public function timeZone(DateTimeZone $timeZone): self
    {
        $this->addField(new TimeZoneField($timeZone));

        return $this;
    }

    public function uid(string $uid): self
    {
        $this->addField(UidField::make($uid));

        return $this;
    }

    public function uuid(string $uuid): self
    {
        return $this->uid($uuid);
    }

    public function calendarAddressUri(string $uri): self
    {
        $this->addField(new CalAdrUriField($uri));

        return $this;
    }

    public function calendarUri(string $uri): self
    {
        $this->addField(new CalUriField($uri));

        return $this;
    }

    public function clientPidMap(int $pid, string $uri): self
    {
        $this->addField(new ClientPidMapField($pid, $uri));

        return $this;
    }

    public function fbUrl(string $uri): self
    {
        $this->addField(new FbUrlField($uri));

        return $this;
    }

    public function impp(string $number, array $types = []): self
    {
        $this->addField(new ImppField($number, $types));

        return $this;
    }

    public function key(string $key): self
    {
        $this->addField(new KeyField($key));

        return $this;
    }

    public function lang(string $lang): self
    {
        $this->addField(LangField::make($lang));

        return $this;
    }

    public function logo(string $logo): self
    {
        $this->addField(new LogoField($logo));

        return $this;
    }

    public function note(string $note): self
    {
        $this->addField(new NoteField($note));

        return $this;
    }

    public function prodid(string $prodid): self
    {
        $this->addField(new ProdidField($prodid));

        return $this;
    }

    public function related(string $related): self
    {
        $this->addField(new RelatedField($related));

        return $this;
    }

    public function revision(DateTimeInterface $dateTime): self
    {
        $this->addField(new RevField($dateTime));

        return $this;
    }

    public function sound(string $sound): self
    {
        $this->addField(new SoundField($sound));

        return $this;
    }

    public function source(string $source): self
    {
        $this->addField(new SourceField($source));

        return $this;
    }

    public function xml(string $xml): self
    {
        $this->addField(new XmlField($xml));

        return $this;
    }

    public function x(string $name, string $value): self
    {
        $this->addField(new XField($name, $value));

        return $this;
    }*/

    public function get()
    {
        $parser = new VCardReader();
        $parser->setCharset('UTF-8');
        return $parser->parse($this->rawData, 0);
    }

    public function __toString(): string
    {
        return $this->get()->serialize();
    }
}
