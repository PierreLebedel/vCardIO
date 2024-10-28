<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Security;

use Pleb\VCardIO\Fields\AbstractField;
use Pleb\VCardIO\Exceptions\VCardParserException;

class ClassField extends AbstractField
{
    protected string $name = 'class';

    protected bool $multiple = false;

    public function __construct(public string $class) {}

    public static function make(string $value, array $attributes = []): self
    {
        $value = strtoupper($value);

        if (!in_array($value, ['PUBLIC', 'PRIVATE', 'CONFIDENTIAL'])) {
            throw VCardParserException::unableToDecodeValue('class', $value);
        }

        return new self($value);
    }

    public function render(): mixed
    {
        return $this->class;
    }

    public function __toString(): string
    {
        return $this->toString($this->class);
    }
}
