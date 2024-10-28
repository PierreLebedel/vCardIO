<?php

namespace Pleb\VCardIO;

use Sabre\VObject\Component\VCard as SabreVCard;

class VCard extends SabreVCard
{

    public static $componentMap = [
        'VCARD' => VCard::class,
    ];

    public function __toString(): string
    {
        return $this->serialize();
    }

}
