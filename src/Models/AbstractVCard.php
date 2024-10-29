<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Models;

use Pleb\VCardIO\VCardProperty;

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
