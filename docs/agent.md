[Documentation homepage](index.md)

# vCard's `AGENT` property

The old school `AGENT` property is not longer supported by the vCard specification, but if you parse old data, you can see something like this, with imbricated vCards:

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
    fn: [...],
    agent: Pleb\VCardIO\Models\VCardV30 {
        version: "3.0"
        fn: [...],
        ...
    },
    ...
}
```

## The vCard object

View the [vCard object documentation](vcard.md).
