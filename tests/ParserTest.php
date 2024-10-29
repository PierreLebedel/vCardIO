<?php

declare(strict_types=1);

use Pleb\VCardIO\Exceptions\VCardParserException;
use Pleb\VCardIO\Models\AbstractVCard;
use Pleb\VCardIO\Models\VCardV21;
use Pleb\VCardIO\Models\VCardV30;
use Pleb\VCardIO\Models\VCardV40;
use Pleb\VCardIO\VCardParser;
use Pleb\VCardIO\VCardsCollection;

use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNull;
use function PHPUnit\Framework\assertSame;

it('make simple collection from raw data', function () {

    $collection = VCardParser::parseRaw('BEGIN:VCARD
VERSION:4.0
FN:John Doe
END:VCARD');

    assertInstanceOf(VCardsCollection::class, $collection);

    expect($collection->count())->toBe(1);

    assertInstanceOf(AbstractVCard::class, $collection[0]);

});

it('make multiple collection from raw data', function () {

    $collection = VCardParser::parseRaw('BEGIN:VCARD
VERSION:4.0
FN:John Doe
END:VCARD
BEGIN:VCARD
VERSION:3.0
FN:Johnny Doe
END:VCARD');

    assertInstanceOf(VCardsCollection::class, $collection);

    expect($collection->count())->toBe(2);

    assertInstanceOf(AbstractVCard::class, $collection[0]);
    assertInstanceOf(AbstractVCard::class, $collection[1]);

});

it('can\'t parse malformatted data', function () {

    $collection = VCardParser::parseRaw('BEGIN:VCARD
VERSION:4.0
FN:John Doe
END:VCARD
BEGIN:VCARD
VERSION:3.0
FN:Johnny Doe');

})->throws(VCardParserException::class);

it('make formatted vCard', function () {

    $collection = VCardParser::parseRaw('BEGIN:VCARD
VERSION:4.0
FN:John Doe
END:VCARD');

    assertInstanceOf(VCardsCollection::class, $collection);

    expect($collection->count())->toBe(1);

    $vCard = $collection[0];

    assertInstanceOf(AbstractVCard::class, $vCard);

    assertSame($vCard->version, '4.0');

    assert($vCard->fn, 'John Doe');
    assertNull($vCard->n);

});

it("can't instanciate vCard with wrong version", function () {

    $collection = VCardParser::parseRaw('BEGIN:VCARD
VERSION:3.5
FN:John Doe
END:VCARD');

})->throws(VCardParserException::class);

it("can't instanciate vCard without version on first line", function () {

    $collection = VCardParser::parseRaw('BEGIN:VCARD
FN:John Doe
VERSION:4.0
END:VCARD');

})->throws(VCardParserException::class);

it('instanciates correct vCard model according version', function () {

    $collection = VCardParser::parseRaw('BEGIN:VCARD
VERSION:2.1
FN:John Doe
END:VCARD
BEGIN:VCARD
VERSION:3.0
FN:John Doe
END:VCARD
BEGIN:VCARD
VERSION:4.0
FN:John Doe
END:VCARD');

    assertInstanceOf(VCardsCollection::class, $collection);

    expect($collection->count())->toBe(3);

    assertInstanceOf(VCardV21::class, $collection[0]);
    assertInstanceOf(VCardV30::class, $collection[1]);
    assertInstanceOf(VCardV40::class, $collection[2]);

});
