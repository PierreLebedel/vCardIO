<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use AllowDynamicProperties;
use Pleb\VCardIO\Enums\VCardVersionEnum;
use Pleb\VCardIO\Fields\AbstractField;
use stdClass;

#[AllowDynamicProperties]
class VCard
{
    public ?VCardVersionEnum $version = null;

    // public stdClass $formattedData;

    // public stdClass $rawData;

    // public stdClass $invalidData;

    // public stdClass $unprocessedData;

    public array $fields = [];

    public function __construct()
    {
        // $this->formattedData = new stdClass;
        // $this->rawData = new stdClass;
        // $this->invalidData = new stdClass;
        // $this->unprocessedData = new stdClass;
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

    public function getVersionFields(): array
    {
        return $this->version?->getFields() ?? [];
    }

    public static function getSingularFields(): array
    {
        return [
            'bday',
            'logo',
            'photo',
            'note',
            'rev',
            'sound',
            'tz',
            'uid',
            'agent',
            'mailer',
            'categories',
            'nickname',
            'class',
            'prodid',
            'anniversary',
            'caladruri',
            'caluri',
            'clientpidmap',
            'fburl',
            'gender',
            'kind',
            'nickname',
            'prodid',
            'related',
            'xml',
        ];
    }

    public function addField(AbstractField $field): self
    {
        if ($field->isMultiple()) {
            if (! array_key_exists($field->name, $this->fields)) {
                $this->fields[$field->name] = [];
            }
            $this->fields[$field->name][] = $field;
        } else {
            $this->fields[$field->name] = $field;
        }

        return $field->apply($this);
    }

    public function __toString(): string
    {
        $propertiesArray = [];

        foreach ([
            'BEGIN:VCARD',
            'VERSION:'.$this->getVersion()->value,
        ] as $property) {
            $propertiesArray[] = $property;
        }

        // $fields = $this->version->getFields();

        dump($this->fields);

        foreach ($this->fields as $name => $fields) {
            if (is_array($fields)) {
                foreach ($fields as $field) {
                    $propertiesArray[] = $field->toString();
                }
            } else {
                $propertiesArray[] = $fields->toString();
            }
        }
        foreach ([
            'END:VCARD',
        ] as $property) {
            $propertiesArray[] = $property;
        }

        return implode(PHP_EOL, $propertiesArray);
    }
}
