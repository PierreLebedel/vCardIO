[Documentation homepage](index.md)

# Exporting files

## vCards

The existing .vcf file will be overwritten.

```php
$vCard->export('./file/export/destination.vcf');
```

## Collections

The existing .vcf can be overwritten or appended.

```php
// OVERWRITTEN
$vCardCollection->export('./file/export/destination.vcf', append:false); 
```
```php
// APPENDED
$vCardCollection->export('./file/export/destination.vcf', append:true); 
```
