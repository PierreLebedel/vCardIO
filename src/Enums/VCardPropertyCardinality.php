<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Enums;

enum VCardPropertyCardinality: string
{
    case ONE_MUST = '1';
    case ONE_MAY = '*1';
    case ONE_MANY_MUST = '1*';
    case ONE_MANY_MAY = '*';

    public function isRequired(): bool
    {
        return match ($this) {
            self::ONE_MUST      => true,
            self::ONE_MANY_MUST => true,
            default             => false,
        };
    }

    public function isMultiple(): bool
    {
        return match ($this) {
            self::ONE_MANY_MUST => true,
            self::ONE_MANY_MAY  => true,
            default             => false,
        };
    }
}
