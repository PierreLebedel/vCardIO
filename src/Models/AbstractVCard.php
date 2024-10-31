<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Models;

use DateTimeImmutable;
use DateTimeZone;
use Pleb\VCardIO\Fields\AbstractField;
use Pleb\VCardIO\VCardProperty;
use stdClass;

abstract class AbstractVCard
{
    public string $version;

    public stdClass $relevantData;

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

    public function __construct()
    {
        $this->relevantData = new stdClass;
    }

    public function applyProperty(VCardProperty $property)
    {
        $this->properties[$property->getName()] = $property;

        return $property->apply($this);
    }

    public function getPrefferedPropertyField(VCardProperty $property): ?AbstractField
    {
        return $this->getPrefferedField($property->getFields());
    }

    public function getPrefferedField(array $fields): ?AbstractField
    {
        if (empty($fields)) {
            return null;
        }

        // return the only one
        if (count($fields) == 1) {
            return reset($fields);
        }

        foreach ($fields as $field) {
            // return the pref
            if ((string) $field->getAttribute('pref') == '1') {
                return $field;
            }
        }

        // return the first
        foreach ($fields as $field) {
            return $field;
        }

        return null;
    }

    public function getRelevantValue(string $name): mixed
    {
        $property = $this->properties[$name] ?? null;

        if (! $property) {
            $property = VCardProperty::find($name);

            if (! $property) {
                return null;
            }

            if ($property->relevantCardinality->isMultiple()) {
                return [];
            }

            return null;
        }

        if ($property->relevantCardinality->isMultiple()) {

            $fields = $property->getFields();

            $response = [];
            if (! empty($fields)) {
                foreach ($fields as $field) {
                    $response[] = $field->relevantRender();
                }
            }

            return array_filter($response);
        }

        return $this->getPrefferedPropertyField($property)?->relevantRender() ?? null;
    }

    public function getFullName(): ?string
    {
        $fullname = $this->getRelevantValue('fn');

        if (! $fullname) {
            if ($name = $this->getName()) {
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

    public function getName(): ?stdClass
    {
        return $this->getRelevantValue('n');
    }

    public function getLastName(): ?string
    {
        return $this->getName()?->lastName ?? null;
    }

    public function getFirstName(): ?string
    {
        return $this->getName()?->firstName ?? null;
    }

    public function getMiddleName(): ?string
    {
        return $this->getName()?->middleName ?? null;
    }

    public function getNamePrefix(): ?string
    {
        return $this->getName()?->namePrefix ?? null;
    }

    public function getNameSuffix(): ?string
    {
        return $this->getName()?->nameSuffix ?? null;
    }

    public function getEmails(): array
    {
        return $this->getRelevantValue('email');
    }

    public function getPhones(): array
    {
        return $this->getRelevantValue('tel');
    }

    public function getUrls(): array
    {
        return $this->getRelevantValue('url');
    }

    public function getPhoto(): ?string
    {
        return $this->getRelevantValue('photo');
    }

    public function getBirthday(): ?DateTimeImmutable
    {
        return $this->getRelevantValue('bday');
    }

    public function getAnniversary(): ?DateTimeImmutable
    {
        return $this->getRelevantValue('anniversary');
    }

    public function getKind(): ?string
    {
        return $this->getRelevantValue('kind');
    }

    public function getGender(): ?string
    {
        return $this->getRelevantValue('gender');
    }

    public function getOrganization(): ?stdClass
    {
        return $this->getRelevantValue('org');
    }

    public function getOrganizationName(): ?string
    {
        return $this->getOrganization()?->organizationName ?? null;
    }

    public function getTitle(): ?string
    {
        return $this->getRelevantValue('title');
    }

    public function getRole(): ?string
    {
        return $this->getRelevantValue('role');
    }

    public function getMember(): ?string
    {
        return $this->getRelevantValue('member');
    }

    public function getAddresses(): array
    {
        return $this->getRelevantValue('adr');
    }

    public function getGeo(): ?stdClass
    {
        return $this->getRelevantValue('geo');
    }

    public function getCategories(): array
    {
        return $this->getRelevantValue('categories') ?? [];
    }

    public function getNicknames(): array
    {
        return $this->getRelevantValue('nickname') ?? [];
    }

    public function getTimeZone(): ?DateTimeZone
    {
        return $this->getRelevantValue('tz');
    }

    public function getUid(): ?string
    {
        return $this->getRelevantValue('uid');
    }

    public function getUuid(): ?string
    {
        return $this->getUid();
    }

    public function getCalendarAddressUri(): ?string
    {
        return $this->getRelevantValue('caladruri');
    }

    public function getCalendarUri(): ?string
    {
        return $this->getRelevantValue('caluri');
    }

    public function getClientPidMap(): array
    {
        $response = [];
        $clientpidmap = $this->getRelevantValue('clientpidmap');
        if (! empty($clientpidmap)) {
            foreach ($clientpidmap as $fieldk => $fieldv) {
                foreach ($fieldv as $pid => $uri) {
                    $response[$pid] = $uri;
                }
            }
        }

        return $response;
    }

    public function getFbUrl(): ?string
    {
        return $this->getRelevantValue('fburl');
    }

    public function getImpps(): array
    {
        return $this->getRelevantValue('impp');
    }

    public function getKey(): ?string
    {
        return $this->getRelevantValue('key');
    }

    public function getLangs(): array
    {
        return $this->getRelevantValue('lang');
    }

    public function getLang(): ?string
    {
        return $this->getRelevantValue('lang');
    }

    public function getLogo(): ?string
    {
        return $this->getRelevantValue('logo');
    }

    public function getNote(): ?string
    {
        return $this->getRelevantValue('note');
    }

    public function getProdid(): ?string
    {
        return $this->getRelevantValue('prodid');
    }

    public function getRelated(): ?string
    {
        return $this->getRelevantValue('related');
    }

    public function getRev(): ?DateTimeImmutable
    {
        return $this->getRelevantValue('rev');
    }

    public function getRevision(): ?DateTimeImmutable
    {
        return $this->getRev();
    }

    public function getSound(): ?string
    {
        return $this->getRelevantValue('sound');
    }

    public function getSource(): ?string
    {
        return $this->getRelevantValue('source');
    }

    public function getXml(): ?string
    {
        return $this->getRelevantValue('xml');
    }

    public function getX(string $name, bool $multiple = false): mixed
    {
        $name = strtolower($name);

        $property = $this->properties['x'] ?? null;
        if (! $property) {
            return ($multiple) ? [] : null;
        }

        $fields = [];
        foreach ($property->getFields() as $field) {
            if ($field->name != $name) {
                continue;
            }
            $fields[] = $field;
        }

        if (empty($fields)) {
            return ($multiple) ? [] : null;
        }

        if ($multiple) {
            $response = [];
            foreach ($fields as $field) {
                $response[] = $field->relevantRender();
            }

            return array_filter($response);
        }

        // singular field
        return $this->getPrefferedField($fields);
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
