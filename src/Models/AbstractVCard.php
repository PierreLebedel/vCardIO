<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Models;

use Pleb\VCardIO\Fields\AbstractField;

abstract class AbstractVCard
{
    public string $version;

    public $adr = null;

    public $birthday = null;

    public array $emails = [];

    public ?string $fullName = null;

    public array $geoLocations = [];

    public $key = null;

    public $logo = null;

    public $name = null;

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

    protected $fields = [];

    public function applyField(AbstractField $field): self
    {
        if( !property_exists($this, $field->getAlias()) ){
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
            if($name=='version') continue;
            foreach ($fields as $field) {
                $vCardString .= (string) $field.PHP_EOL;
            }
        }

        $vCardString .= 'BEGIN:VCARD';

        return $vCardString;
    }

    public function __toString()
    {
        return $this->toString();
    }
}
