<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use DateTimeZone;
use stdClass;

class VCard
{
    public ?float $version = null;

    public ?stdClass $n = null;

    public ?string $fn = null;

    public array $nickname = [];

    public array $adr = [];

    public ?stdClass $bday = null;

    public array $categories = [];

    public array $email = [];

    public array $tel = [];

    public ?stdClass $geo = null;

    public ?stdClass $org = null;

    public ?DateTimeZone $tz = null;

    public ?string $xml = null;

    public $agent = null;

    public array $unparsedData = [];
}
