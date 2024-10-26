<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Enums;

enum VCardVersionEnum: string
{
    case V21 = '2.1';
    case V30 = '3.0';
    case V40 = '4.0';

    public function getDataFields(): array
    {
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

        if ($this->value == '2.1') {
            $dataFields += [
                'agent'  => 'agent',
                'label'  => 'label',
                'lang'   => 'lang',
                'mailer' => 'mailer',
            ];

        } elseif ($this->value == '3.0') {
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

        } elseif ($this->value == '4.0') {
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

        ksort($dataFields);

        return $dataFields;
    }
}
