<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Models;

use Pleb\VCardIO\Fields\AbstractField;

abstract class AbstractVCard
{
    public string $version;

    public $adr = null;

    public $bday = null;

    public ?array $emails = null;

    public ?string $fullName = null;

    public $geo = null;

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

    public function applyField(AbstractField $field): self
    {
        return $field->apply($this);
    }

    public function toString(): string
    {
        return 'todo tostring';
    }

    public function __toString()
    {
        return $this->toString();
    }
}
