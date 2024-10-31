<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields;

abstract class AbstractField
{
    public bool $hasAttributes = false;

    public array $possibleAttributes = [];

    public array $defaultAttributes = [];

    public function __construct(public string $value, public array $attributes = []) {}

    public function cleanValue(): string
    {
        return $this->value;
    }

    public function cleanAttributes(): array
    {
        if (! empty($this->attributes)) {
            foreach ($this->attributes as $k => $v) {
                if (empty($v)) {
                    unset($this->attributes[$k]);
                }
            }
        }

        if (array_key_exists('charset', $this->attributes)) {
            if (! empty($this->attributes['charset']) && $this->attributes['charset'] != 'UTF-8') {
                $this->value = mb_convert_encoding($this->value, 'UTF-8', $this->attributes['charset']);
            }
            unset($this->attributes['charset']);
        }

        if (! empty($this->possibleAttributes)) {
            foreach ($this->possibleAttributes as $k => $v) {
                if (! array_key_exists($k, $this->attributes)) {
                    $this->attributes[$k] = ($k == 'type') ? [] : null;
                }
            }
        }

        if (! empty($this->defaultAttributes)) {
            foreach ($this->defaultAttributes as $k => $v) {
                if (! array_key_exists($k, $this->attributes) || empty($this->attributes[$k])) {
                    $this->attributes[$k] = $v;
                }
            }
        }

        return $this->attributes;
    }

    public function setHasAttributes(bool $hasAttributes = true): self
    {
        $this->hasAttributes = $hasAttributes;

        return $this;
    }

    public function setPossibleAttributes(array $possibleAttributes = []): self
    {
        $this->possibleAttributes = $possibleAttributes;
        $this->setHasAttributes(true);
        $this->cleanAttributes();

        return $this;
    }

    public function setDefaultAttributes(array $defaultAttributes = []): self
    {
        $this->defaultAttributes = $defaultAttributes;
        $this->setHasAttributes(true);
        $this->cleanAttributes();

        return $this;
    }

    public function getAttribute(string $name) :mixed
    {
        $this->cleanAttributes();

        if(array_key_exists($name, $this->attributes)){
            return $this->attributes[$name];
        }

        if(array_key_exists($name, $this->defaultAttributes)){
            return $this->defaultAttributes[$name];
        }

        return null;
    }

    abstract public function render(): mixed;

    abstract public function relevantRender(): mixed;

    public function __toString(): string
    {
        $fieldString = '';

        if (! empty($this->cleanAttributes())) {
            foreach ($this->attributes as $k => $v) {
                if (empty($v)) {
                    continue;
                }
                $fieldString .= ';'.strtoupper($k).'=';
                $fieldString .= is_array($v) ? strtoupper(implode(',', array_values($v))) : strtoupper((string) $v);
            }
        }

        $fieldString .= ':'.$this->cleanValue();

        return $fieldString;
    }
}
