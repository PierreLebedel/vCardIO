<?php

declare(strict_types=1);

use Pleb\VCardIO\Models\AbstractVCard;
use Pleb\VCardIO\Models\VCardV30;
use Pleb\VCardIO\Models\VCardV40;
use Pleb\VCardIO\VCardParser;

use function PHPUnit\Framework\assertInstanceOf;

it('can get first collection item as vcard object', function () {

    $collection = VCardParser::parseRaw('BEGIN:VCARD
VERSION:4.0
FN:Jeffrey Lebowski
END:VCARD');

    assertInstanceOf(AbstractVCard::class, $collection->first());
    assertInstanceOf(VCardV40::class, $collection->first());

});

it('can get indexed collection item as vcard object', function () {

    $collection = VCardParser::parseRaw('BEGIN:VCARD
VERSION:4.0
FN:Jeffrey Lebowski
END:VCARD
BEGIN:VCARD
VERSION:3.0
FN:Walter Sobchak
END:VCARD');

    assertInstanceOf(AbstractVCard::class, $collection->getVCard(0));
    assertInstanceOf(VCardV40::class, $collection->getVCard(0));

    assertInstanceOf(AbstractVCard::class, $collection->getVCard(1));
    assertInstanceOf(VCardV30::class, $collection->getVCard(1));

});

it('can get collection count', function () {

    $collection = VCardParser::parseRaw('BEGIN:VCARD
VERSION:4.0
FN:Jeffrey Lebowski
END:VCARD
BEGIN:VCARD
VERSION:3.0
FN:Walter Sobchak
END:VCARD');

    expect($collection->count())->toBe(2);

});
