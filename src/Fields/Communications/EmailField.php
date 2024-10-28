<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Communications;

use Pleb\VCardIO\Fields\AbstractField;
use stdClass;

class Emailfield extends AbstractField
{
    protected string $name = 'email';

    protected ?string $alias = 'emails';

    protected bool $multiple = true;

    public function __construct(public string $email, public array $types = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value, $attributes['type'] ?? []);
    }

    public static function getPossibleAttributes(): array
    {
        return [
            'pid',
            'pref',
            'type',
            'altid',
        ];
    }

    public function render(): mixed
    {
        $object = new stdClass;
        $object->value = $this->email;
        $object->types = $this->types;

        return $object;
    }

    public function __toString(): string
    {
        return $this->toString($this->email, ['type' => $this->types]);
    }
}
