<?php

declare(strict_types=1);

use Pleb\VCardIO\Models\AbstractVCard;
use Pleb\VCardIO\Models\VCardV40;
use Pleb\VCardIO\VCardBuilder;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;

it('can build vcard', function () {

    $builder = VCardBuilder::make()
        ->fullName('Jeffrey Lebowski');

    assertInstanceOf(VCardBuilder::class, $builder);

    assertInstanceOf(AbstractVCard::class, $builder->get());
    assertInstanceOf(VCardV40::class, $builder->get());

    //     $string = 'BEGIN:VCARD
    // VERSION:4.0
    // FN:Jeffrey Lebowski
    // END:VCARD';

    //     assertEquals($string, (string)$builder->get());

});
