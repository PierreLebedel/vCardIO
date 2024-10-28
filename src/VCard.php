<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use AllowDynamicProperties;
use Pleb\VCardIO\Enums\VCardVersionEnum;
use Pleb\VCardIO\Fields\AbstractField;
use stdClass;

#[AllowDynamicProperties]
class VCard
{
    public static function getSingularFields(): array
    {
        return [
            'caladruri',
            'caluri',
            'class',
            'clientpidmap',
            'fburl',
            'gender',
            'kind',
            'logo',
            'mailer',
            'prodid',
        ];
    }
}
