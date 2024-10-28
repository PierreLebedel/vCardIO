<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Explanatory;

use Pleb\VCardIO\Enums\VCardVersionEnum;
use Pleb\VCardIO\Exceptions\VCardException;
use Pleb\VCardIO\Fields\AbstractField;

class VersionField extends AbstractField
{
    protected string $name = 'version';

    protected bool $multiple = false;

    public function __construct(public VCardVersionEnum $versionEnum) {}

    public static function make(string $value, array $attributes = []): self
    {
        $versionEnum = VCardVersionEnum::tryFrom($value);
        if (! $versionEnum) {
            throw VCardException::invalidVersion($value);
        }

        return new self(VCardVersionEnum::from($value));
    }

    public function render(): mixed
    {
        return $this->versionEnum->value;
    }

    public function __toString(): string
    {
        return $this->toString($this->versionEnum->value);
    }
}
