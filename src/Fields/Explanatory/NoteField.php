<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Explanatory;

use Pleb\VCardIO\Fields\AbstractField;

class NoteField extends AbstractField
{
    protected string $name = 'note';

    protected bool $multiple = false;

    public function __construct(public string $note, public array $attributes = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value, $attributes);
    }

    public function render(): mixed
    {
        return $this->note;
    }

    public function __toString(): string
    {
        return $this->toString($this->note, $this->attributes);
    }
}
