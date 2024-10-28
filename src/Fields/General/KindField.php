<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\General;

use Pleb\VCardIO\Exceptions\VCardParserException;
use Pleb\VCardIO\Fields\AbstractField;

class KindField extends AbstractField
{
    protected string $name = 'kind';

    protected bool $multiple = false;

    public function __construct(public string $kind) {}

    public static function make(string $value, array $attributes = []): self
    {
        if (! in_array($value, ['individual', 'group', 'org', 'location', 'iana-token', 'x-name'])) {
            throw VCardParserException::unableToDecodeValue('kind', $value);
        }

        return new self($value);
    }

    public function render(): mixed
    {
        return $this->kind;
    }

    public function __toString(): string
    {
        return $this->toString($this->kind);
    }
}
