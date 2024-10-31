<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Models;

use DateTimeImmutable;
use DateTimeZone;
use Pleb\VCardIO\VCardProperty;
use Pleb\VCardIO\Fields\AbstractField;
use stdClass;

abstract class AbstractVCard
{
    public string $version;

    public $adr = null;

    public $bday = null;

    public $email = null;

    public $fn = null;

    public $geo = null;

    public $key = null;

    public $logo = null;

    public $n = null;

    public $note = null;

    public $org = null;

    public $photo = null;

    public $rev = null;

    public $role = null;

    public $sound = null;

    public $tel = null;

    public $title = null;

    public $tz = null;

    public $uid = null;

    public $url = null;

    public $x = null;

    protected $properties = [];

    public function applyProperty(VCardProperty $property)
    {
        $this->properties[$property->getName()] = $property;

        return $property->apply($this);
    }

    public function getPropertyFields(string $name) :array
    {
        $property = $this->properties[$name] ?? null;
        if(!$property) return [];

        return $property->getFields();
    }

    public function getPrefferedPropertyField(string $name) :?AbstractField
    {
        $fields = $this->getPropertyFields($name);
        if( empty($fields) ){
            return null;
        }

        // return the only one
        if(count($fields) == 1){
            return reset($fields);
        }

        foreach($fields as $field){
            // return the pref
            if($field->getAttribute('pref')=='1'){
                return $field;
            }
        }

        // return the first
        foreach($fields as $field){
            return $field;
        }

        return null;
    }

    public function getPropertyRelevantValue(string $name) :mixed
    {
        return $this->getPrefferedPropertyField($name)?->getRelevantValue() ?? null;
    }

    public function getPropertyRelevantValues(string $name) :?array
    {
        $fields = $this->getPropertyFields($name);
        $response = [];
        if(!empty($fields)){
            foreach($fields as $field){
                $response[] = $field->getRelevantValue();
            }
        }
        return array_filter( $response );
    }

    public function getFullName() :?string
    {
        $fullname = $this->getPropertyRelevantValue('fn');

        if(!$fullname){
            if($name = $this->getName()){
                $fullname = trim(implode(' ', array_filter([
                    $name->namePrefix,
                    $name->firstName,
                    $name->middleName,
                    $name->lastName,
                    $name->nameSuffix,
                ])));
            }
        }

        return $fullname;
    }

    public function getName() :?stdClass
    {
        return $this->getPropertyRelevantValue('n');
    }

    public function getLastName() :?string
    {
        return $this->getName()?->lastName ?? null;
    }

    public function getFirstName() :?string
    {
        return $this->getName()?->firstName ?? null;
    }

    public function getMiddleName() :?string
    {
        return $this->getName()?->middleName ?? null;
    }

    public function getNamePrefix() :?string
    {
        return $this->getName()?->namePrefix ?? null;
    }

    public function getNameSuffix() :?string
    {
        return $this->getName()?->nameSuffix ?? null;
    }

    public function getEmails(): array
    {
        return $this->getPropertyRelevantValues('email');
    }

    public function getPhones(): array
    {
        return $this->getPropertyRelevantValues('tel');
    }

    public function getUrls(): array
    {
        return $this->getPropertyRelevantValues('url');
    }

    public function getPhoto(): ?string
    {
        return $this->getPropertyRelevantValue('photo');
    }

    public function getBirthday(): ?DateTimeImmutable
    {
        return $this->getPropertyRelevantValue('bday');
    }

    public function getAnniversary(): ?DateTimeImmutable
    {
        return $this->getPropertyRelevantValue('anniversary');
    }

    public function getKind(): ?string
    {
        return $this->getPropertyRelevantValue('kind');
    }

    public function getGender(): ?string
    {
        return $this->getPropertyRelevantValue('gender');
    }

    public function getOrganization(): ?stdClass
    {
        return $this->getPropertyRelevantValue('org');
    }

    public function getOrganizationName(): ?string
    {
        return $this->getOrganization()?->organizationName ?? null;
    }

    public function getTitle(): ?string
    {
        return $this->getPropertyRelevantValue('title');
    }

    public function getRole(): ?string
    {
        return $this->getPropertyRelevantValue('role');
    }

    public function getMember(): ?string
    {
        return $this->getPropertyRelevantValue('member');
    }

    public function getAddresses(): array
    {
        return $this->getPropertyRelevantValues('adr');
    }

    public function getGeo(): ?stdClass
    {
        return $this->getPropertyRelevantValue('geo');
    }

    public function getCategories(): array
    {
        return $this->getPropertyRelevantValue('categories') ?? [];
    }

    public function getNicknames(): array
    {
        return $this->getPropertyRelevantValue('nickname') ?? [];
    }

    public function getTimeZone(): ?DateTimeZone
    {
        return $this->getPropertyRelevantValue('tz');
    }

    public function getUid(): ?string
    {
        return $this->getPropertyRelevantValue('uid');
    }

    public function getUuid(): ?string
    {
        return $this->getUid();
    }

    public function getCalendarAddressUri(): ?string
    {
        return $this->getPropertyRelevantValue('caladruri');
    }

    public function getCalendarUri(): ?string
    {
        return $this->getPropertyRelevantValue('caluri');
    }

    public function getClientPidMap() :array
    {
        $response = [];
        $clientpidmap = $this->getPropertyRelevantValues('clientpidmap');
        if(!empty($clientpidmap)){
            foreach($clientpidmap as $fieldk => $fieldv){
                foreach($fieldv as $pid => $uri){
                    $response[$pid] = $uri;
                }
            }
        }
        return $response;
    }

    public function getFbUrl(): ?string
    {
        return $this->getPropertyRelevantValue('fburl');
    }

    public function getImpps(): array
    {
        return $this->getPropertyRelevantValues('impp');
    }

    public function getKey(): ?string
    {
        return $this->getPropertyRelevantValue('key');
    }

    public function getLangs(): array
    {
        return $this->getPropertyRelevantValues('lang');
    }

    public function getLang(): ?string
    {
        return $this->getPropertyRelevantValue('lang');
    }

    public function getLogo(): ?string
    {
        return $this->getPropertyRelevantValue('logo');
    }

    public function getNote(): ?string
    {
        return $this->getPropertyRelevantValue('note');
    }

    public function getProdid(): ?string
    {
        return $this->getPropertyRelevantValue('prodid');
    }

    public function getRelated(): ?string
    {
        return $this->getPropertyRelevantValue('related');
    }

    public function getRev(): ?DateTimeImmutable
    {
        return $this->getPropertyRelevantValue('rev');
    }

    public function getRevision(): ?DateTimeImmutable
    {
        return $this->getRev();
    }

    public function getSound(): ?string
    {
        return $this->getPropertyRelevantValue('sound');
    }

    public function getSource(): ?string
    {
        return $this->getPropertyRelevantValue('source');
    }

    public function getXml(): ?string
    {
        return $this->getPropertyRelevantValue('xml');
    }

    public function getX(string $name, bool $multiple = false): mixed
    {
        $name = strtolower($name);

        $allXFields = $this->getPropertyFields('x');
        if(empty($allXFields)){
            return ($multiple) ? [] : null;
        }

        $fields = [];
        foreach($allXFields as $field){
            if( $field->name != $name ) continue;
            $fields[] = $field;
        }

        if(empty($fields)){
            return ($multiple) ? [] : null;
        }

        if($multiple){
            $response = [];
            foreach($fields as $field){
                $response[] = $field->getRelevantValue();
            }
            return array_filter($response);
        }

        // return the only one
        if(count($fields) == 1){
            return reset($fields)->getRelevantValue();
        }

        foreach($fields as $field){
            // return the pref
            if($field->getAttribute('pref')=='1'){
                return $field->getRelevantValue();
            }
        }

        // return the first
        foreach($fields as $field){
            return $field->getRelevantValue();
        }

        return null;
    }

    public function toString(): string
    {
        $vCardString = 'BEGIN:VCARD'.PHP_EOL;
        $vCardString .= 'VERSION:'.$this->version.PHP_EOL;

        foreach ($this->properties as $name => $property) {
            if ($name == 'version') {
                continue;
            }
            $vCardString .= (string) $property.PHP_EOL;
        }

        $vCardString .= 'END:VCARD';

        return $vCardString;
    }

    public function __toString()
    {
        return $this->toString();
    }
}
