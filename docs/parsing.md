[Documentation homepage](index.md)

# Parsing data

## Parse .vcf file

```php
$vCardsCollection = Pleb\VCardIO\VCardParser::parseFile('./contacts.vcf');
```

## Parse raw data

Give a string to the parser to obtain same result.

```php
$vCardsRawData = 'BEGIN:VCARD
VERSION:4.0
FN:Jeffrey Lebowski
BDAY:19421204
X-MAIN-HOBBY:Bowling
END:VCARD';

$vCardsCollection = Pleb\VCardIO\VCardParser::parseRaw($vCardsRawData);
```

## Return format

The result of parsing is a VCardsCollection object.

View the [collection documentation](collection.md) for more information.
