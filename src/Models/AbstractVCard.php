<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Models;

use DateTimeInterface;
use DateTimeZone;
use Pleb\VCardIO\Fields\AbstractField;
use Pleb\VCardIO\Fields\Identification\FullNameField;
use stdClass;

abstract class AbstractVCard
{
    public string $version;

    public $addresses = null;

    public $birthday = null;

    public $emails = null;

    public $fullName = null;

    public $geoLocations = null;

    public $key = null;

    public $logo = null;

    public $name = null;

    public $note = null;

    public $organizations = null;

    public $photo = null;

    public $revision = null;

    public $role = null;

    public $sound = null;

    public $phones = null;

    public $title = null;

    public $timeZone = null;

    public $uid = null;

    public $url = null;

    public ?stdClass $x = null;

    protected $fields = [];

    public function applyField(AbstractField $field): self
    {
        if (! property_exists($this, $field->getAlias())) {
            dump('property not exists alias:'.$field->getAlias().' in '.get_class($this));

            return $this;
        }

        if (! array_key_exists($field->getName(), $this->fields)) {
            $this->fields[$field->getName()] = [];
        }

        if (! $field->isMultiple()) {
            $this->fields[$field->getName()][0] = $field;
        } else {
            $this->fields[$field->getName()][] = $field;
        }

        return $field->apply($this);
    }

    public function toString(): string
    {
        $vCardString = 'BEGIN:VCARD'.PHP_EOL;
        $vCardString .= 'VERSION:'.$this->version.PHP_EOL;

        foreach ($this->fields as $name => $fields) {
            if ($name == 'version') {
                continue;
            }
            foreach ($fields as $field) {
                $vCardString .= (string) $field.PHP_EOL;
            }
        }

        $vCardString .= 'END:VCARD';

        return $vCardString;
    }

    public function __toString()
    {
        return $this->toString();
    }
}
