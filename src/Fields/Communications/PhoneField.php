<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Communications;

use Pleb\VCardIO\Fields\AbstractField;
use stdClass;

class PhoneField extends AbstractField
{
    protected string $name = 'tel';

    protected ?string $alias = 'phones';

    protected bool $multiple = true;

    // ['home', 'msg', 'work', 'pref', 'voice', 'fax', 'cell', 'video', 'pager', 'bbs', 'modem', 'car', 'isdn', 'pcs']

    public function __construct(public string $number, public array $types = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value, $attributes['type'] ?? []);
    }

    public function render(): mixed
    {
        $object = new stdClass;
        $object->value = $this->number;
        $object->types = $this->types;

        return $object;
    }

    public function __toString(): string
    {
        return $this->toString($this->number, ['type' => $this->types]);
    }
}
