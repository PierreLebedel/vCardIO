<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Enums;

use Pleb\VCardIO\Models\VCardV21;
use Pleb\VCardIO\Models\VCardV30;
use Pleb\VCardIO\Models\VCardV40;
use Pleb\VCardIO\Fields\NameField;
use Pleb\VCardIO\Fields\FullNameField;

enum VCardVersionEnum: string
{
    case V21 = '2.1';
    case V30 = '3.0';
    case V40 = '4.0';

    public function getVCardClass() :string
    {
        return match ($this) {
            self::V21 => VCardV21::class,
            self::V30 => VCardV30::class,
            self::V40 => VCardV40::class,
        };
    }

}
