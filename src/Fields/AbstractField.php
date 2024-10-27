<?php

namespace Pleb\VCardIO\Fields;

use Pleb\VCardIO\VCard;
use Pleb\VCardIO\Models\AbstractVCard;
use Pleb\VCardIO\Exceptions\VCardFieldException;
use Pleb\VCardIO\VCardParser;

abstract class AbstractField
{

    protected string $name;
    protected ?string $alias = null;
    protected bool $multiple;

    public static function parse(string $rawData): ?AbstractField
    {
        @[$nameAttributes, $value] = explode(':', $rawData, 2);
        if (empty($value)) {
            throw VCardFieldException::emptyValue();
        }

        $value = $value;
        $attributes = explode(';', $nameAttributes);
        $name = mb_strtolower($attributes[0]);
        array_shift($attributes);

        $attributes = self::parseAttributes($attributes);

        $fieldClass = VCardParser::fields()[$name] ?? null;

        if(!$fieldClass || !class_exists($fieldClass)){
            dump('@todo field for name:'.$name);
            return null;
            //throw VCardFieldException::unknownField($name);
        }

        return $fieldClass::make($value, $attributes);
    }

    protected static function parseAttributes(array $attributes = []) :array
    {
        if (empty($attributes)) {
            return [];
        }

        $newAttributes = [];
        foreach ($attributes as $attribute) {

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

        return $newAttributes;
    }

    public function getName() :string
    {
        return $this->name;
    }

    public function getAlias() :string
    {
        return $this->alias ?? $this->name;
    }

    public function isMultiple() :bool
    {
        return $this->multiple;
    }

    public static function getDefaultValue() :mixed
    {
        return null;
    }

    public function apply(AbstractVCard $vCard) :AbstractVCard
    {
        if($this->multiple){
            $vCard->{$this->getAlias()}[] = $this->render();
        }else{
            $vCard->{$this->getAlias()} = $this->render();
        }

        return $vCard;
    }

    public function toString(string $value, array $attributes = []) :string
    {
        $attributes = self::parseAttributes($attributes);

        $property = implode(';', array_merge([strtoupper($this->getName())], $attributes));
        $property .= ':'.$value;

        return $property;
    }

    abstract public static function make(string $value, array $attributes = []) :self;

    abstract public function render() :mixed;

    abstract public function __toString() :string;

}
