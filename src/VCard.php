<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use Sabre\VObject\Component\VCard as SabreVCard;
use Sabre\VObject\UUIDUtil;

class VCard extends SabreVCard
{
    public static $componentMap = [
        'VCARD' => self::class,
    ];

    protected function getDefaults()
    {
        return [
            'VERSION' => '4.0',
            'PRODID'  => '-//PLeb//VCardIO VCardIO '.VCardLibrary::VERSION.'//EN',
            'UID'     => UUIDUtil::getUUID(),
        ];
    }

    public function __toString(): string
    {
        return $this->serialize();
    }
}
