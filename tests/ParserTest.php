<?php

declare(strict_types=1);

use Pleb\VCardIO\Exceptions\VCardException;
use Pleb\VCardIO\VCard;
use Pleb\VCardIO\VCardParser;

use Pleb\VCardIO\VCardsCollection;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertNull;

it('can test', function () {
    expect(true)->toBeTrue();
});

it('make simple collection from raw data', function(){

    $collection = VCardParser::parseRaw('BEGIN:VCARD
VERSION:4.0
FN:John Doe
END:VCARD');

    assertInstanceOf(VCardsCollection::class, $collection);

    expect($collection->count())->toBe(1);

    assertInstanceOf(VCard::class, $collection[0]);

});

it('make multiple collection from raw data', function(){

    $collection = VCardParser::parseRaw('BEGIN:VCARD
VERSION:4.0
FN:John Doe
END:VCARD
BEGIN:VCARD
VERSION:3.0
FN:John Doe
END:VCARD');

    assertInstanceOf(VCardsCollection::class, $collection);

    expect($collection->count())->toBe(2);

    assertInstanceOf(VCard::class, $collection[0]);
    assertInstanceOf(VCard::class, $collection[1]);

});

it('make formatted vCard', function(){

    $collection = VCardParser::parseRaw('BEGIN:VCARD
VERSION:4.0
FN:John Doe
END:VCARD');

    assertInstanceOf(VCardsCollection::class, $collection);

    expect($collection->count())->toBe(1);

    $vCard = $collection[0];

    assertInstanceOf(VCard::class, $vCard);

    assert($vCard->getVersion(), '4.0');
    assert($vCard->formattedData->fullName, 'John Doe');
    assertNull($vCard->formattedData->name);

});

it("can't instanciate vCard with wrong version", function(){

    $collection = VCardParser::parseRaw('BEGIN:VCARD
VERSION:3.5
FN:John Doe
END:VCARD');

})->throws(VCardException::class)->only();
