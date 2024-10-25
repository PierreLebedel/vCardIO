<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use DateTime;
use DateTimeZone;
use stdClass;

class VCardField
{
    public ?string $name = null;

    public ?string $value = null;

    public mixed $formattedValue = null;

    public ?string $formattedName = null;

    public mixed $rawValue = null;

    public array $attributes = [];

    protected $isString = false;

    protected $isArray = false;

    protected $isObject = false;

    protected $isMultiple = false;

    protected $isUnprocessed = false;

    public function __construct(public string $rawContents)
    {
        @[$nameAttributes, $value] = explode(':', $this->rawContents, 2);
        if (empty($value)) {
            return;
        }

        $this->value = $value;
        $this->attributes = explode(';', $nameAttributes);
        $this->name = mb_strtolower($this->attributes[0]);
        $this->formattedName = $this->name;
        array_shift($this->attributes);

        $this->parseAttributes();

        if (array_key_exists('charset', $this->attributes) && ! empty($this->attributes['charset'])) {
            $this->value = mb_convert_encoding($this->value, 'UTF-8', $this->attributes['charset']);
        }
    }

    protected function parseAttributes()
    {
        if (empty($this->attributes)) {
            return;
        }

        $newAttributes = [];
        foreach ($this->attributes as $attribute) {

            $keyValues = explode('=', $attribute, 2);

            if (count($keyValues) === 2) {
                $key = strtolower($keyValues[0]);
                $values = explode(',', $keyValues[1]);
            } elseif (count($keyValues) === 1) {
                $key = 'type';
                $values = explode(',', $keyValues[0]);
            } else {
                continue;
            }

            if (! array_key_exists($key, $newAttributes)) {
                $newAttributes[$key] = null;
            }

            if (count($values) <= 1 && ! in_array($key, ['type'])) {
                $newAttributes[$key] = $values[0] ?? null;
            } else {
                $newAttributes[$key] = $values;
            }
        }

        $this->attributes = $newAttributes;
    }

    public function string(): self
    {
        $this->isString = true;
        $this->formattedValue = $this->value;
        $this->rawValue = $this->value;

        return $this;
    }

    public function uri(): self
    {
        $this->isString = true;
        $this->formattedValue = $this->value;
        $this->rawValue = $this->value;

        if (preg_match('/base64/', strtolower($this->value))) {
            //$test = base64_decode($this->value);
        } elseif (preg_match('/encoding=b/', strtolower($this->value))) {
            // $test = base64_decode($this->value);
        } elseif (preg_match('/quoted-printable/', strtolower($this->value))) {
            // $test = quoted_printable_decode($this->value);
        }

        return $this;
    }

    public function array(array $keys): self
    {
        $this->isArray = true;

        $this->formattedValue = new stdClass;

        $values = explode(';', $this->value, count($keys));
        foreach ($keys as $index => $key) {
            $this->formattedValue->{$key} = $values[$index] ?? null;
        }

        $this->rawValue = array_values((array) $this->formattedValue);

        return $this;
    }

    public function object(): self
    {
        $this->isObject = true;

        $this->formattedValue = new stdClass;
        $this->formattedValue->value = $this->value;

        $this->rawValue = $this->value;

        return $this;
    }

    public function ltrim(array $stringsToLtrim): self
    {
        if (is_string($this->formattedValue)) {
            foreach ($stringsToLtrim as $string) {
                if (strpos($this->formattedValue, $string) === 0) {
                    $this->formattedValue = substr($this->value, strlen($string));
                }
            }
        }

        return $this;
    }

    public function coordinates(): self
    {
        $this->isArray = true;

        $this->rawValue = $this->value;

        $coordinates = null;

        if (strpos($this->value, 'geo:') === 0) {
            $input = substr($this->value, 4);
            $coordinates = explode(',', $input);

        } elseif (strpos($this->value, ';') !== false) {
            $coordinates = explode(';', $this->value);
        } elseif (strpos($this->value, ',') !== false) {
            $coordinates = explode(',', $this->value);
        }

        if (! $coordinates || count($coordinates) < 2) {
            $this->formattedValue = null;

            return $this;
        }

        $this->formattedValue = new stdClass;
        $this->formattedValue->latitude = $coordinates[0];
        $this->formattedValue->longitude = $coordinates[1];

        return $this;
    }

    public function multiple(): self
    {
        $this->isMultiple = true;

        $this->addAttribute('pref', range(1, 100));

        return $this;
    }

    public function datetime(): self
    {
        $this->isString = true;

        if (substr($this->value, 0, 2) == '--') {
            $this->value = str_replace('--', date('Y'), $this->value);
        }

        if (strlen($this->value) == 18 && (str_contains($this->value, '-') || str_contains($this->value, '+'))) {
            $this->formattedValue = DateTime::createFromFormat('Ymd\THiO', $this->value);
        } elseif (strlen($this->value) == 16) {
            $this->formattedValue = DateTime::createFromFormat('Ymd\THis\Z', $this->value);
        } elseif (strlen($this->value) == 10) {
            $this->formattedValue = DateTime::createFromFormat('Y-m-d', $this->value);
        } elseif (strlen($this->value) == 8) {
            $this->formattedValue = DateTime::createFromFormat('Ymd', $this->value);
        } elseif (strlen($this->value) == 6) {
            $this->formattedValue = DateTime::createFromFormat('ymd', $this->value);
        }

        $this->rawValue = $this->value;

        return $this;
    }

    public function timezone(): self
    {
        $this->formattedValue = new DateTimeZone($this->value) ?? null;

        $this->rawValue = $this->value;

        return $this;
    }

    public function as(string $formattedName): self
    {
        $this->formattedName = $formattedName;

        return $this;
    }

    public function in(array $stringPossibilities): self
    {
        if (! is_string($this->value) || ! in_array($this->value, $stringPossibilities)) {
            $this->formattedValue = null;
        }

        return $this;
    }

    public function addAttribute(string $attribute, array $constrainedBy = []): self
    {
        if ($attribute == 'type' && array_key_exists('type', $this->attributes)) {
            if (in_array('pref', array_map('strtolower', $this->attributes['type']))) {
                $this->formattedValue->attributes['pref'] = 1;
            }
        }

        if (! empty($constrainedBy)) {
            if (array_key_exists($attribute, $this->attributes)) {
                if (is_array($this->attributes[$attribute])) {
                    $foundAttributes = array_intersect($this->attributes[$attribute], $constrainedBy) ?? null;
                } elseif (in_array($this->attributes[$attribute], $constrainedBy)) {
                    $foundAttributes = $this->attributes[$attribute] ?? null;
                } else {
                    $foundAttributes = null;
                }
            } else {
                $foundAttributes = null;
            }
        } else {
            $foundAttributes = $this->attributes[$attribute] ?? null;
        }

        if ($this->formattedValue instanceof stdClass) {

            if (! property_exists($this->formattedValue, 'attributes')) {
                $this->formattedValue->attributes = [];
            }

            $this->formattedValue->attributes[$attribute] = $foundAttributes ?? null;
        }

        // if(is_string($this->formattedValue)){
        //     $stringObject = new stdClass;
        //     $stringObject->value = $this->formattedValue;
        //     $stringObject->attributes[$attribute] = $foundAttributes ?? null;

        //     $this->formattedValue = $stringObject;
        // }

        return $this;
    }

    public function unprocecced(): self
    {
        $this->isUnprocessed = true;

        return $this;
    }

    public function render(VCard $vCard)
    {
        if ($this->isUnprocessed) {
            $vCard->unprocessedData->{$this->name} = $this->rawContents;

            return;
        }

        if (! property_exists($vCard->rawData, $this->name)) {
            $vCard->invalidData->{$this->name} = $this->rawContents;

            return;
        }

        if (! property_exists($vCard->formattedData, $this->formattedName)) {
            $vCard->invalidData->{$this->name} = '[formatted] '.$this->rawContents;

            return;
        }

        $rawDataObject = new stdClass;
        $rawDataObject->attributes = $this->attributes;
        $rawDataObject->value = $this->rawValue;

        if ($this->isMultiple) {
            if ($this->isString) {
                $vCard->rawData->{$this->name}[] = $this->rawValue;
            } else {
                $vCard->rawData->{$this->name}[] = $rawDataObject;
            }
        } else {
            if ($this->isString) {
                $vCard->rawData->{$this->name} = $this->rawValue;
            } else {
                $vCard->rawData->{$this->name} = $rawDataObject;
            }
        }

        if ($this->isMultiple) {
            if (is_string($this->formattedValue)) {
                $vCard->formattedData->{$this->formattedName} = explode(',', $this->formattedValue);
            } else {
                $vCard->formattedData->{$this->formattedName}[] = $this->formattedValue;
            }

        } else {
            $vCard->formattedData->{$this->formattedName} = $this->formattedValue;

        }
    }
}
