<?php

namespace Pleb\VCardIO\Fields;

use Pleb\VCardIO\VCard;
use Pleb\VCardIO\Models\AbstractVCard;

class SourceNameField extends AbstractField
{

    protected string $name = 'name';
    protected ?string $alias = 'sourceName';
    protected bool $multiple = false;

    public function __construct(public string $sourceName)
    {}

    public static function make(string $value, array $attributes = []) :self
    {
        return new self($value);
    }

    public function render() :mixed
    {
        return $this->sourceName;
    }

    public function __toString() :string
    {
        return $this->toString($this->sourceName);
    }

}
