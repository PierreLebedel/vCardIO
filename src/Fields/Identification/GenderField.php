<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Identification;

use Pleb\VCardIO\Exceptions\VCardParserException;
use Pleb\VCardIO\Fields\AbstractField;

class GenderField extends AbstractField
{
    protected string $name = 'gender';

    protected bool $multiple = false;

    public function __construct(public string $gender) {}

    public static function make(string $value, array $attributes = []): self
    {
        $value = strtoupper($value);

        if (! in_array($value, ['M', 'F', 'O', 'N', 'U'])) {
            throw VCardParserException::unableToDecodeValue('gender', $value);
        }

        return new self($value);
    }

    public function render(): mixed
    {
        return $this->gender;
    }

    public function __toString(): string
    {
        return $this->toString($this->gender);
    }
}
