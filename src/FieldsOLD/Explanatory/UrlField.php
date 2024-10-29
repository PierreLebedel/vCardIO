<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Explanatory;

use Pleb\VCardIO\Fields\AbstractField;

class UrlField extends AbstractField
{
    protected string $name = 'url';

    protected bool $multiple = true;

    public function __construct(public string $url, public array $attributes = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value, $attributes);
    }

    public function render(): mixed
    {
        return $this->url;
    }

    public function __toString(): string
    {
        return $this->toString($this->url, $this->attributes);
    }
}
