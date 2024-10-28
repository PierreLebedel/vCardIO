<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\General;

use Pleb\VCardIO\Fields\AbstractField;

class XmlField extends AbstractField
{
    protected string $name = 'xml';

    protected bool $multiple = false;

    public function __construct(public string $xml) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value);
    }

    public function render(): mixed
    {
        return $this->xml;
    }

    public function __toString(): string
    {

        return $this->toString($this->xml);
    }
}
