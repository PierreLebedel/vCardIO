<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\General;

use Pleb\VCardIO\Exceptions\VCardParserException;
use Pleb\VCardIO\Fields\AbstractField;

class ProfileField extends AbstractField
{
    protected string $name = 'profile';

    protected bool $multiple = true;

    public function __construct(public string $profile) {}

    public static function make(string $value, array $attributes = []): self
    {
        $value = strtoupper($value);

        if (! in_array($value, ['VCARD'])) {
            throw VCardParserException::unableToDecodeValue('profile', $value);
        }

        return new self($value);
    }

    public function render(): mixed
    {
        return $this->profile;
    }

    public function __toString(): string
    {
        return $this->toString($this->profile);
    }
}
