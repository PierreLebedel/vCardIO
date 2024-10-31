<?php

declare(strict_types=1);

use Pleb\VCardIO\VCardParser;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertEqualsCanonicalizing;
use function PHPUnit\Framework\assertIsArray;
use function PHPUnit\Framework\assertNull;

it('can read vcard relevant data', function () {

    $collection = VCardParser::parseRaw('BEGIN:VCARD
VERSION:4.0
FN:Jeffrey Lebowski
N:Lebowski;Jeffrey;The Dude;;
NICKNAME:The Dude,His Dudeness,El Duderino
END:VCARD');

    $vCard = $collection->first();

    assertEquals('Jeffrey Lebowski', $vCard->getFullName());
    assertEquals('Lebowski', $vCard->getLastName());
    assertEquals('Jeffrey', $vCard->getFirstName());
    assertEquals('The Dude', $vCard->getMiddleName());
    assertNull($vCard->getNamePrefix());
    assertNull($vCard->getNameSuffix());

    assertIsArray($vCard->getNicknames());
    assertEqualsCanonicalizing($vCard->getNicknames(), [
        'The Dude',
        'El Duderino',
        'His Dudeness',
    ]);

});

it('can read vcard fullname from name parts', function () {

    $collection = VCardParser::parseRaw('BEGIN:VCARD
VERSION:4.0
N:Lebowski;Jeffrey
END:VCARD');

    $vCard = $collection->first();

    assertEquals('Jeffrey Lebowski', $vCard->getFullName());

});
