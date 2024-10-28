<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Explanatory;

use Pleb\VCardIO\Fields\AbstractField;

class ProdIdField extends AbstractField
{
    protected string $name = 'prodid';

    protected ?string $alias = null;

    protected bool $multiple = false;

    public function __construct(public string $prodid) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value);
    }

    public function render(): mixed
    {
        return $this->prodid;
    }

    public function __toString(): string
    {
        return $this->toString($this->prodid);
    }
}
