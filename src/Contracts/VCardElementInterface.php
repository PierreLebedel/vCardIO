<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Contracts;

interface VCardElementInterface
{
    public function __construct(string $inputValue);

    public function isMultiple(): bool;

    public function outputValue(): mixed;
}
