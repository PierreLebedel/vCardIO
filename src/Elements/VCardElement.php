<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Elements;

use Pleb\VCardIO\Contracts\VCardElementInterface;

class VCardElement implements VCardElementInterface
{
    public ?array $restrictedTypes = null;

    public function __construct(public string $inputValue, public array $types = []) {}

    public function isMultiple(): bool
    {
        return false;
    }

    public function outputValue(): mixed
    {
        return $this->inputValue;
    }

    public function typed(array $restrictedTypes): static
    {
        $this->restrictedTypes = $restrictedTypes;

        return $this;
    }
}
