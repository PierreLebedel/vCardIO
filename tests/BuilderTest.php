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

    $vCard = $builder->get();

    assertInstanceOf(VCardBuilder::class, $builder);
    assertInstanceOf(AbstractVCard::class, $vCard);
    assertInstanceOf(VCardV40::class, $vCard);
    assertEquals('Jeffrey Lebowski', $vCard->getFullName());
    assertStringContainsString('BEGIN:VCARD', (string) $vCard);
    assertStringContainsString('VERSION:4.0', (string) $vCard);
    assertStringContainsString('FN:Jeffrey Lebowski', (string) $vCard);
    assertStringContainsString('END:VCARD', (string) $vCard);
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

it('exports utf-8 vcards with windows-safe line endings', function () {
    $path = sys_get_temp_dir().DIRECTORY_SEPARATOR.'vcardio-accented.vcf';

    VCardBuilder::make()
        ->version('3.0')
        ->fullName('Élodie Brûlé')
        ->name('Brûlé', 'Élodie')
        ->organization('Société Générale')
        ->export($path);

    $raw = file_get_contents($path);

    expect($raw)->not->toBeFalse();

    expect(substr($raw, 0, 3))->not->toBe("\xEF\xBB\xBF");
    assertStringContainsString("BEGIN:VCARD\r\nVERSION:3.0\r\n", $raw);
    assertStringContainsString("FN:Élodie Brûlé\r\n", $raw);
    assertStringContainsString("N:Brûlé;Élodie\r\n", $raw);
    assertStringContainsString("ORG:Société Générale\r\n", $raw);
    expect($raw)->toContain("\r\nEND:VCARD");
});
