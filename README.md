# vCardIO - Read & write vCard (vcf) files

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

### Parse vCards

```php
$vCardsCollection = VCardParser::parseFile('./contacts.vcf');

// OR

$vCardsRawData = 'BEGIN:VCARD
VERSION:4.0
FN:John Doe
BDAY:19020202
X-CUSTOM-VALUE;TYPE=MD:ReadMe
END:VCARD';
$vCardsCollection = Pleb\VCardIO\VCardParser::parseRaw($vCardsRawData);

// RESULT:

Pleb\VCardIO\VCardsCollection {
    vCards: [
        Pleb\VCardIO\Models\VCardV40 {
            version: '4.0'
            fn: [
                {
                    value: 'John Doe',
                    attributes: []
                }
            ],
            bday: {
                dateTime: DateTime @-2143152000,
                format: "Ymd",
                formatted: "190200202",
                extactYear: true
            },
            x: [
                {
                    name: "custom-value"
                    value: "ReadMe"
                    attributes: [
                        'type' => ['md']
                    ]
                }
            ],
            ...
        },
    ],
}
```

#### Support of old school Agent property

The `AGENT` property is not longer supported by the vCard specification, but if you parse old data, you can see something like this, with imbricated vCards:

```txt
BEGIN:VCARD
VERSION:3.0
FN:John Doe
AGENT:BEGIN:VCARD
 VERSION:3.0
 FN:John David
 END:VCARD
END:VCARD
```

This package will parse it as a `VCards`'s `agent` property:

```php
Pleb\VCardIO\VCardsCollection {
    vCards: [
        Pleb\VCardIO\Models\VCardV30 {
            version: '3.0'
            fn: [...],
            agent: Pleb\VCardIO\Models\VCardV30 {
                version: "3.0"
                fn: [...],
                ...
            },
            ...
        },
    ],
}
```

### Build vCards

A large set of methods is implemented on the vCard builder to set all the properties fluently.

```php
$vCard = Pleb\VCardIO\VCardBuilder::make()
    ->fullname('John Doe')
    ->birthday(new DateTime('1902-02-02'))
    ->x('custom-value', 'ReadMe')
    ->x('other', 'VCF')
    ->get();
```
### Print vCards

You can use `(string) $vCard` to display vCard contents :

```php
echo nl2br((string) $vCard);
```
```txt
BEGIN:VCARD
VERSION:4.0
FN:John Doe
BDAY:19020202
X-CUSTOM-VALUE:ReadMe
X-OTHER:VCF
REV:20241029
PRODID:-//Pleb//Pleb vCardIO 1.0.0 //EN
END:VCARD
```

The same is true for vCards collections :

```php
$vCardsCollection = VCardParser::parseFile('./contacts.vcf');
echo nl2br((string) $vCardsCollection);
```
```txt
BEGIN:VCARD
VERSION:4.0
FN:John Doe
END:VCARD
BEGIN:VCARD
VERSION:3.0
FN:John Doed
END:VCARD
...
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
