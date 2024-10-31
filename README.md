# vCardIO - Parse, read, manipulate & write vCard (.vcf files)

[![Latest Version on Packagist](https://img.shields.io/packagist/v/pleb/vcardio.svg?style=flat-square)](https://packagist.org/packages/pleb/vcardio)
[![Tests](https://img.shields.io/github/actions/workflow/status/PierreLebedel/vCardIO/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/PierreLebedel/vCardIO/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/pleb/vcardio.svg?style=flat-square)](https://packagist.org/packages/pleb/vcardio)

This package based on [RFC 6350](https://datatracker.ietf.org/doc/html/rfc6350) is intended to simplify the parsing and the manipulation of vCard objects, coming from .vcf files or from raw data.
It allows you to build vCard objects & export them to text or .vcf files.

## Installation

Via composer:

```bash
composer require pleb/vcardio
```

## Documentation

- [Parsing data](docs/parsing.md)
- [vCards collection](docs/collection.md)
- [vCard builder](docs/builder.md)
- [vCard object](docs/vcard.md)

## Usage

### Parsing data

You can parse vCards objects from .vcf file or from raw data, and obtain an iterable collection of vCards.

```php
$vCardsCollection = Pleb\VCardIO\VCardParser::parseFile('./contacts.vcf');

// OR

$vCardsRawData = 'BEGIN:VCARD
VERSION:4.0
FN:Jeffrey Lebowski
BDAY:19421204
X-MAIN-HOBBY:Bowling
END:VCARD';

$vCardsCollection = Pleb\VCardIO\VCardParser::parseRaw($vCardsRawData);
```

### The vCards collection

The result of parsing is a collection of vCards:

```php
Pleb\VCardIO\VCardsCollection {
    vCards: [
        Pleb\VCardIO\Models\VCardV40 {
            version: "4.0",
            relevantData: {
                version: "4.0",
                fn: "Jeffrey Lebowski",
                bday: DateTimeImmutable @-854466859,
                x: {
                    mainHobby: "Bowling"
                }
            },
            fn: [
                {
                    value: "Jeffrey Lebowski",
                    attributes: []
                }
            ],
            bday: {
                dateTime: DateTimeImmutable @-854466859,
                format: "Ymd",
                formatted: "19421204",
                extactYear: true,
                attributes: []
            },
            x: [
                {
                    name: "main-hobby",
                    formattedName: "mainHobby",
                    value: "Bowling",
                    attributes: []
                }
            ],
            ...
        },
    ],
}
```

#### Manually build a vCards collection

```php
$vCardsCollection = (new Pleb\VCardIO\VCardsCollection())
    ->addVCard($vCard1)
    ->addVCard($vCard2);

// OR

$vCardsCollection = new Pleb\VCardIO\VCardsCollection([$vCard1, $vCard2]);
```

#### Manipulate collection

The `VCardsCollection` object implements `ArrayAccess`, `Iterator` and `Countable` interfaces, so you can loop on it.

```php
foreach($vCardsCollection as $vCard){
    // ...
}
// OR
$vCard = $vCardsCollection->first();
// OR
$vCard = $vCardsCollection->getVCard(0); // 1,2,...
```

### The vCard object

A huge set of methods is implemented to read the vCard properties. You can see all available getters methods [on the vCard object documentation](docs/vcard.md).

```php
$vCard->getFullName();                      // :?string
$vCard->getLastName();                      // :?string
$vCard->getFirstName();                     // :?string
$vCard->getEmails();                        // :array<string>
$vCard->getPhones();                        // :array<string>
$vCard->getX('main-hobby', multiple:false); // :?string|array
$vCard->getX('main-hobby', multiple:true);  // :array
// ...
```

#### Note on "Pseudo-singular" properties 

[RFC 6350](https://datatracker.ietf.org/doc/html/rfc6350) allows most of properties to be present multiple times in a vCard. For example the `FN` (fullName) property can be present 1 or multiple times, and accompagnied by attributes to distinct them.

In this package, we assume that some of properties (like fullName) got a **unique main value**. The vCard's *`getProperty()`* methods will return this main value, as well as the sub-object `$vCard->relevantData`.

The complete set of value is stil available in the root of `$vCard` object.

#### Note on the old school `AGENT` property

The `AGENT` property is not longer supported by the vCard specification, but if you parse old data, you can see something like this, with nested vCards:

```txt
BEGIN:VCARD
VERSION:3.0
FN:Jeffrey Lebowski
AGENT:BEGIN:VCARD
 VERSION:3.0
 FN:Walter Sobchak
 END:VCARD
END:VCARD
```

This package will parse it as a VCard's `agent` property:

```php
Pleb\VCardIO\Models\VCardV30 {
    version: '3.0'
    // ...,
    agent: Pleb\VCardIO\Models\VCardV30 {
        version: "3.0"
        // ...
    },
    // ...
}
```

### The vCard builder

You can create your vCard objects from scratch fluently by using the large set of methods implemented on the vCard builder. You can see all available setters methods [on the vCard builder documentation](docs/builder.md).

```php
$vCard = Pleb\VCardIO\VCardBuilder::make()
    ->fullName('Jeffrey Lebowski')
    ->nickName('The Dude')
    ->birthday(new DateTime('1942-12-04'))
    ->x('main-hobby', 'Bowling')
    ->get();
```

Each method returns the builder instance, so you can chain them.

Use the `get()` method to get your vCard.

### Print vCards

#### Print a single vCard

You can use `(string) $vCard` to display vCard contents:

```php
echo nl2br((string) $vCard);
```
```txt
BEGIN:VCARD
VERSION:4.0
FN:Jeffrey Lebowski
NICKNAME:The Dude
BDAY:19421204
X-HOBBY:Bowling
REV:20241029
PRODID:-//Pleb//Pleb vCardIO 1.1.0 //EN
END:VCARD
```

#### Print vCards collection

The same is true for vCards collections, what will display the vCards serially:

```php
echo nl2br((string) $vCardsCollection);
```
```txt
BEGIN:VCARD
VERSION:4.0
FN:Jeffrey Lebowski
END:VCARD
BEGIN:VCARD
VERSION:3.0
FN:Walter Sobchak
END:VCARD
...
```

## Contribute

### Code formatting
```bash
composer format
```

### Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
