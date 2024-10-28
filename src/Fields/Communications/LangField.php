<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Communications;

use Pleb\VCardIO\Fields\AbstractField;
use stdClass;

class LangField extends AbstractField
{
    protected string $name = 'lang';

    protected ?string $alias = 'langs';

    protected bool $multiple = true;

    public function __construct(public string $lang, public array $types = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value, $attributes['type'] ?? []);
    }

    public function render(): mixed
    {
        $object = new stdClass;
        $object->value = $this->lang;
        $object->types = $this->types;

        return $object;
    }

    public function __toString(): string
    {
        return $this->toString($this->lang, ['type' => $this->types]);
    }
}
