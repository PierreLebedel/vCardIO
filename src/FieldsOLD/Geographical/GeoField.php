<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Geographical;

use Pleb\VCardIO\Exceptions\VCardParserException;
use Pleb\VCardIO\Fields\AbstractField;
use stdClass;

class GeoField extends AbstractField
{
    protected string $name = 'geo';

    protected ?string $alias = 'geoLocations';

    protected bool $multiple = true;

    public function __construct(public float $latitude, public float $longitude, public array $attributes = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        if (strpos($value, 'geo:') === 0) {
            $input = substr($value, 4);
        }

        if (strpos($input, ';') !== false) {
            $input = explode(';', $input)[0];
        }

        $coordinates = explode(',', $input, 2);

        if (count($coordinates) != 2) {
            throw VCardParserException::unableToDecodeValue('coordinates', $value);
        }

        return new self(floatval($coordinates[0]), floatval($coordinates[1]), $attributes);
    }

    public static function getPossibleAttributes(): array
    {
        return [
            'pid',
            'pref',
            'mediatype',
            'type',
            'altid',
        ];
    }

    public function render(): mixed
    {
        $object = new stdClass;
        $object->latitude = $this->latitude;
        $object->longitude = $this->longitude;
        $object->attributes = $this->attributes;

        return $object;
    }

    public function __toString(): string
    {
        $geoLink = 'geo:'.$this->latitude.','.$this->longitude;

        return $this->toString($geoLink, $this->attributes);
    }
}
