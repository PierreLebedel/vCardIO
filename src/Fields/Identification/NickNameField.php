<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Identification;

use Pleb\VCardIO\Fields\AbstractField;

class NickNameField extends AbstractField
{
    protected string $name = 'nickname';

    protected ?string $alias = 'nickNames';

    protected bool $multiple = false;

    public function __construct(public array $nickNames) {}

    public static function make(string $value, array $attributes = []): self
    {
        $nickNames = explode(',', $value);
        return new self($nickNames);
    }

    public function render(): mixed
    {
        return $this->nickNames;
    }

    public function __toString(): string
    {

        return $this->toString(implode(',', $this->nickNames));
    }
}
