<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Identification;

use Pleb\VCardIO\Fields\AbstractField;
use Pleb\VCardIO\Formatters\TextFormatter;

class FullNameField extends AbstractField
{
    protected string $name = 'fn';

    protected ?string $alias = 'fullName';

    protected bool $multiple = true;

    public function __construct(public string $fullName, public array $attributes = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value, $attributes);
    }

    public function render(): mixed
    {
        //return $this->fullName;

        return new TextFormatter($this->fullName, $this->attributes);
    }

    public function __toString(): string
    {
        return $this->toString($this->fullName, $this->attributes);
    }
}
