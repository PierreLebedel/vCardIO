<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Contracts;

interface VCardStructuredElementInterface
{
    public function definition(): array;
}
