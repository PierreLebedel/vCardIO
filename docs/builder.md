[Documentation homepage](index.md)

# Builder

## Build a vCard

Each `VCardBuilder` setter returns the builder instance so you can chain them.

```php
$vCard = Pleb\VCardIO\VCardBuilder::make()
    ->fullName('Jeffrey Lebowski')
    ->nickName('The Dude')
    ->birthday(new DateTime('1942-12-04'))
    ->x('main-hobby', 'Bowling')
    ->get();
```

## Available setters

<!--
Method | Params | Description
--- | --- | ---
`version` | `string\|VCardVersionEnum` | Set the vCard version.
`agent` | `string\|AbstractVCard` | Set the agent.
`fullName` | `string` | Set the full name.
-->

```php
$vCardBuilder->version(string|VCardVersionEnum $version);

$vCardBuilder->agent(string|AbstractVCard $agent);

$vCardBuilder->fullName(?string $fullName);

$vCardBuilder->name(
    ?string $lastName = null,
    ?string $firstName = null,
    ?string $middleName = null,
    ?string $namePrefix = null,
    ?string $nameSuffix = null
);

$vCardBuilder->lastName(string $lastName);

$vCardBuilder->firstName(string $firstName);

$vCardBuilder->middleName(string $middleName);

$vCardBuilder->namePrefix(string $namePrefix);

$vCardBuilder->nameSuffix(string $nameSuffix);

$vCardBuilder->email(string $email, array $types = []);

$vCardBuilder->phone(string $number, array $types = ['voice']);

$vCardBuilder->url(string $url);

$vCardBuilder->photo(string $photo);

$vCardBuilder->bday(DateTimeInterface $bday);
$vCardBuilder->birthday(DateTimeInterface $bday);

$vCardBuilder->anniversary(DateTimeInterface $anniversary);

$vCardBuilder->kind(string $kind);

$vCardBuilder->gender(string $gender);

$vCardBuilder->organization(string $company, ?string $unit1, ?string $unit2);

$vCardBuilder->title(string $title);

$vCardBuilder->role(string $role);

$vCardBuilder->member(string $uid);

$vCardBuilder->address(
    ?string $postOfficeAddress,
    ?string $extendedAddress,
    ?string $street,
    ?string $locality,
    ?string $region,
    ?string $postalCode,
    ?string $country,
    array $types = []
);

$vCardBuilder->geo(float $latitude, float $longitude);

$vCardBuilder->categories(array $categories);

$vCardBuilder->category(string $category);

$vCardBuilder->nickNames(array $nickNames);

$vCardBuilder->nickName(string $nickName);

$vCardBuilder->timeZone(DateTimeZone $timeZone);

$vCardBuilder->uid(string $uid);
$vCardBuilder->uuid(string $uuid);

$vCardBuilder->calendarAddressUri(string $uri);

$vCardBuilder->calendarUri(string $uri);

$vCardBuilder->clientPidMap(int $pid, string $uri);

$vCardBuilder->fbUrl(string $url);

$vCardBuilder->impp(string $number, array $types = []);

$vCardBuilder->key(string $key);

$vCardBuilder->lang(string $lang, int $pref = 1);

$vCardBuilder->langs(array $langs);

$vCardBuilder->logo(string $logo);

$vCardBuilder->note(string $note);

$vCardBuilder->prodid(string $prodid);

$vCardBuilder->related(string $related);

$vCardBuilder->rev(DateTimeInterface $dateTime);
$vCardBuilder->revision(DateTimeInterface $dateTime);

$vCardBuilder->sound(string $sound);

$vCardBuilder->source(string $source);

$vCardBuilder->xml(string $xml);

$vCardBuilder->x(string $name, string $value);

```

## Get the vCard object

```php
$vCard = Pleb\VCardIO\VCardBuilder::make()
    // ...
    ->get();
```

View the [vCard object documentation](vcard.md) for more information.
