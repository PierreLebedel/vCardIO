<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use stdClass;

class VCard
{
    public ?string $version = null;

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

    public function setVersion(string $version): self
    {
        $this->version = $version;
        $this->initVersionData();

        return $this;
    }

    public function getVersion(): ?string
    {
        return $this->version;
    }

    public function getDataFields(): array
    {
        if (! $this->version) {
            return [];
        }

        $dataFields = [
            'fn'      => 'fullName',
            'n'       => 'name',
            'adr'     => 'addresses',
            'bday'    => 'birthday',
            'email'   => 'emails',
            'geo'     => 'geo',
            'key'     => 'key',
            'logo'    => 'logo',
            'n'       => 'name',
            'note'    => 'note',
            'org'     => 'organization',
            'photo'   => 'photo',
            'rev'     => 'revision',
            'role'    => 'role',
            'sound'   => 'sound',
            'tel'     => 'phones',
            'title'   => 'title',
            'tz'      => 'timezone',
            'uid'     => 'uid',
            'url'     => 'url',
            'version' => 'version',
        ];

        if ($this->version == '2.1') {
            $dataFields += [
                'agent'  => 'agent',
                'label'  => 'label',
                'lang'   => 'lang',
                'mailer' => 'mailer',
            ];

        } elseif ($this->version == '3.0') {
            $dataFields += [
                'agent'       => 'agent',
                'categories'  => 'categories',
                'class'       => 'class',
                'impp'        => 'impp',
                'label'       => 'label',
                'mailer'      => 'mailer',
                'name'        => 'name',
                'nickname'    => 'nicknames',
                'prodid'      => 'prodid',
                'profile'     => 'profile',
                'sort-string' => 'sort-string',
                'source'      => 'source',
            ];

        } elseif ($this->version == '4.0') {
            $dataFields += [
                'anniversary'  => 'anniversary',
                'caladruri'    => 'caladruri',
                'caluri'       => 'caluri',
                'categories'   => 'categories',
                'clientpidmap' => 'clientpidmap',
                'fburl'        => 'fburl',
                'gender'       => 'gender',
                'impp'         => 'impp',
                'kind'         => 'kind',
                'lang'         => 'langs',
                'member'       => 'member',
                'nickname'     => 'nicknames',
                'prodid'       => 'prodid',
                'related'      => 'related',
                'source'       => 'source',
                'xml'          => 'xml',
            ];
        }

        return $dataFields;
    }

    public function initVersionData(): self
    {
        $dataFields = $this->getDataFields();

        if (empty($dataFields)) {
            return $this;
        }

        foreach ($dataFields as $field => $alias) {
            $this->formattedData->{$alias} = null;
            $this->rawData->{$field} = null;
        }

        return $this;
    }
}
