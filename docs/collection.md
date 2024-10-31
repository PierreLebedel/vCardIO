[Documentation homepage](index.md)

# vCards collection

## the VCardsCollection object



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

## Build a vCards collection

Pass the vCards array to the constructor:
```php
$vCardsCollection = new Pleb\VCardIO\VCardsCollection([$vCard1, $vCard2]);
```

Or add vCards fluently:
```php
$vCardsCollection = (new Pleb\VCardIO\VCardsCollection())
    ->addVCard($vCard1)
    ->addVCard($vCard2);
```



## Retreive collection vCard objects

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

```php
echo $vCardsCollection->count(); // int
```

View the [vCard object documentation](vcard.md) for more information.

## Render a collection

You can use `(string) $vCard` to display vCard contents:

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
