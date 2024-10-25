# Read & write vCard (vcf) files

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pleb/vcardio.svg?style=flat-square)](https://packagist.org/packages/pleb/vcardio)
[![Tests](https://img.shields.io/github/actions/workflow/status/PierreLebedel/vCardIO/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/PierreLebedel/vCardIO/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/pleb/vcardio.svg?style=flat-square)](https://packagist.org/packages/pleb/vcardio)

This package can read vCard from files (.vcf) or from raw data, and it can write a formatted vCard file from vCard objects.

## Installation

You can install the package via composer:

```bash
composer require pleb/vcardio
```

## Usage

```php
$vCardsCollection = VCardParser::parseFile('./contacts.vcf');

// OR

$vCardsRawData = 'BEGIN:VCARD
VERSION:4.0
FN:John Doe
END:VCARD';
$vCardsCollection = Pleb\VCardIO\VCardParser::parseRaw($vCardsRawData);

// RESULT:

Pleb\VCardIO\VCardsCollection {
    vCards: [
        Pleb\VCardIO\VCard {
            version: '4.0'
            formattedData: {
                fullName: 'John Doe',
                // ...
            },
            rawData: {
                fn: 'John Doe',
                // ...
            },
            invalidData: {},
            unprocessedData: {},
        },
    ],
}
```

## Code formatting

```bash
composer format
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
