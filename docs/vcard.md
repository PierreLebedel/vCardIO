[Documentation homepage](index.md)

# vCard

## Model classes

Each vCard standard version got its model class, which extends the AbstractVCard:

Version | Class
---     | ---
2.1     | VCardV21
3.0     | VCardV30
4.0     | VCardV40

## vCard object description 

```php
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
```

## Available getters methods

> [!NOTE]
> **"Pseudo-singular" properties:** [RFC 6350](https://datatracker.ietf.org/doc/html/rfc6350) allows most of properties to be present multiple times in a vCard. For example the `FN` (fullName) property can be present 1 or multiple times, and accompagnied by attributes to distinct them.
> In this package, we assume that some of properties (like fullName) got a **unique main value**. The vCard's *`getProperty()`* methods will return this main value, as well as the sub-object `$vCard->relevantData`.
> The complete set of value is stil available in the root of `$vCard` object.

```php
$vCard->getFullName(): ?string;

$vCard->getName(): ?stdClass;

$vCard->getLastName(): ?string;

$vCard->getFirstName(): ?string;

$vCard->getMiddleName(): ?string;

$vCard->getNamePrefix(): ?string;

$vCard->getNameSuffix(): ?string;

$vCard->getEmails(): array;

$vCard->getPhones(): array;

$vCard->getUrls(): array;

$vCard->getPhoto(): ?string;

$vCard->getBirthday(): ?DateTimeImmutable;

$vCard->getAnniversary(): ?DateTimeImmutable;

$vCard->getKind(): ?string;

$vCard->getGender(): ?string;

$vCard->getOrganization(): ?stdClass;

$vCard->getOrganizationName(): ?string;

$vCard->getTitle(): ?string;

$vCard->getRole(): ?string;

$vCard->getMember(): ?string;

$vCard->getAddresses(): array;

$vCard->getGeo(): ?stdClass;

$vCard->getCategories(): array;

$vCard->getNicknames(): array;

$vCard->getTimeZone(): ?DateTimeZone;

$vCard->getUid(): ?string;
$vCard->getUuid(): ?string;

$vCard->getCalendarAddressUri(): ?string;

$vCard->getCalendarUri(): ?string;

$vCard->getClientPidMap(): array;

$vCard->getFbUrl(): ?string;

$vCard->getImpps(): array;

$vCard->getKey(): ?string;

$vCard->getLangs(): array;

$vCard->getLang(): ?string;

$vCard->getLogo(): ?string;

$vCard->getNote(): ?string;

$vCard->getProdid(): ?string;

$vCard->getRelated(): ?string;

$vCard->getRev(): ?DateTimeImmutable;
$vCard->getRevision(): ?DateTimeImmutable;

$vCard->getSound(): ?string;

$vCard->getSource(): ?string;

$vCard->getXml(): ?string;

$vCard->getX(string $name, multiple:false): ?string;
$vCard->getX(string $name, multiple:true): array;
```

## Render a vCard

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

## Note on the Agent property

The `AGENT` property is not longer supported by the vCard specification, but you can use it as a child vCard object.

View the [agent property documentation](agent.md) for more information.
