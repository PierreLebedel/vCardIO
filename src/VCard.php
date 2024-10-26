<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use Pleb\VCardIO\Enums\VCardVersionEnum;
use stdClass;

class VCard
{
    public ?VCardVersionEnum $version = null;

    public stdClass $formattedData;

    public stdClass $rawData;

    public stdClass $invalidData;

    public stdClass $unprocessedData;

    public function __construct()
    {
        $this->formattedData = new stdClass;
        $this->rawData = new stdClass;
        $this->invalidData = new stdClass;
        $this->unprocessedData = new stdClass;
    }

    public function setVersion(VCardVersionEnum $version): self
    {
        $this->version = $version;
        $this->initVersionData();

        return $this;
    }

    public function getVersion(): ?VCardVersionEnum
    {
        return $this->version;
    }

    public function getDataFields(): array
    {
        return $this->version?->getDataFields() ?? [];
    }

    public static function getSingularFields(): array
    {
        return [
            'version',
            'fn',
            'n',
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

    public function initVersionData(): self
    {
        if (! $this->version) {
            return [];
        }

        $dataFields = $this->version->getDataFields();

        if (empty($dataFields)) {
            return $this;
        }

        foreach ($dataFields as $field => $alias) {
            $this->formattedData->{$alias} = null;
            $this->rawData->{$field} = null;
        }

        return $this;
    }

    public function __toString(): string
    {
        return implode(PHP_EOL, [
            'BEGIN:VCARD',
            'VERSION:'.$this->getVersion()->value,
            'FN;CHARSET=UTF-8:Todo ToString',
            'PRODID:-//Pleb vCardIO',
            'END:VCARD',
        ]);
    }
}
