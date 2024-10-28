<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields;

use Pleb\VCardIO\Exceptions\VCardFieldException;
use Pleb\VCardIO\Models\AbstractVCard;
use Pleb\VCardIO\VCardParser;

abstract class AbstractField
{
    protected string $name;

    protected ?string $alias = null;

    protected bool $multiple;

    public static function makeFromRaw(string $rawData): ?AbstractField
    {
        @[$nameAttributes, $value] = explode(':', $rawData, 2);

        $attributes = explode(';', $nameAttributes);
        $name = mb_strtolower($attributes[0]);
        array_shift($attributes);

        if (empty($value)) {
            dump('@todo empty value for name:'.$name);

            return null;
        }

        $attributes = self::parseAttributes($attributes);

        $fieldClass = VCardParser::fieldsMap()[$name] ?? null;

        if (! $fieldClass || ! class_exists($fieldClass)) {
            dump('@todo field for name:'.$name);

            return null;
            //throw VCardFieldException::unknownField($name);
        }

        return $fieldClass::make($value, $attributes);
    }

    protected static function parseAttributes(array $attributes = []): array
    {
        if (empty($attributes)) {
            return [];
        }

        $newAttributes = [];
        foreach ($attributes as $k => $v) {

            if(is_numeric($k) && is_string($v)){
                $keyValues = explode('=', $v, 2);

                if (count($keyValues) === 2) {
                    $k = strtolower($keyValues[0]);
                    $v = explode(',', $keyValues[1]);
                } elseif (count($keyValues) === 1) {
                    $k = 'type';
                    $v = explode(',', $keyValues[0]);
                } else {
                    continue;
                }
            }

            if (! array_key_exists($k, $newAttributes)) {
                $newAttributes[$k] = null;
            }

            if(is_array($v) && count($v) <= 1 && ! in_array($k, ['type'])){
                $newAttributes[$k] = $v[0] ?? null;
            } else {
                $newAttributes[$k] = $v;
            }
        }

        return $newAttributes;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAlias(): string
    {
        return $this->alias ?? $this->name;
    }

    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    public static function getDefaultValue(): mixed
    {
        return null;
    }

    public function apply(AbstractVCard $vCard): AbstractVCard
    {
        if ($this->multiple) {
            $vCard->{$this->getAlias()}[] = $this->render();
        } else {
            $vCard->{$this->getAlias()} = $this->render();
        }

        return $vCard;
    }

    public function toString(string $value, array $attributes = []): string
    {
        $attributes = self::parseAttributes($attributes);

        $property = strtoupper($this->getName());

        if(!empty($attributes)){
            foreach($attributes as $k=>$v){
                if(empty($v)) continue;
                $property .= ';'.$k.'=';
                $property .= is_array($v) ? implode(',', array_values($v)) : $v;
            }
        }

        $property .= ':'.$value;

        return $property;
    }

    abstract public static function make(string $value, array $attributes = []): self;

    abstract public function render(): mixed;

    abstract public function __toString(): string;
}
