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

    public mixed $rawValue = null;

    public array $attributes = [];

    protected $isString = false;

    protected $isArray = false;

    protected $isObject = false;

    protected $isMultiple = false;

    protected $isUnprocessed = false;

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

    public function array(): self
    {
        $this->formattedValue = explode(',', $this->value);
        $this->rawValue = explode(',', $this->value);

        return $this;
    }

    public function assoc(array $keys): self
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
                $this->attributes['pref'] = 1;
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

        if (! array_key_exists($this->name, $vCard->getVersionFields())) {
            $vCard->invalidData->{$this->name} = $this->rawContents;

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

        $alias = $vCard->getVersionFields()[$this->name];

        if ($this->isMultiple) {
            if (is_string($this->formattedValue)) {
                $vCard->formattedData->{$alias} = explode(',', $this->formattedValue);
            } else {
                $vCard->formattedData->{$alias}[] = $this->formattedValue;
            }

        } else {
            $vCard->formattedData->{$alias} = $this->formattedValue;
        }

    }
}
