<?php

declare(strict_types=1);

use Pleb\VCardIO\Models\AbstractVCard;
use Pleb\VCardIO\Models\VCardV40;
use Pleb\VCardIO\VCardBuilder;

use function PHPUnit\Framework\assertEquals;
use function PHPUnit\Framework\assertInstanceOf;
use function PHPUnit\Framework\assertStringContainsString;

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

it('can conditionally chain setters with when', function () {
    $builder = VCardBuilder::make()
        ->when(true, fn (VCardBuilder $builder): VCardBuilder => $builder->fullName('Jeffrey Lebowski'))
        ->when(false, fn (VCardBuilder $builder): VCardBuilder => $builder->email('hidden@example.com'))
        ->when(
            false,
            fn (VCardBuilder $builder): VCardBuilder => $builder->phone('+33102030405'),
            fn (VCardBuilder $builder): VCardBuilder => $builder->email('jeff@example.com')
        );

    $vCard = $builder->get();

    assertEquals('Jeffrey Lebowski', $vCard->getFullName());
    assertStringContainsString('EMAIL:jeff@example.com', (string) $vCard);
});
