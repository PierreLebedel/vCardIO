<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use stdClass;

class VCard
{
    public stdClass $formattedData;

    public stdClass $rawData;

    public stdClass $unexpectedData;

    public function __construct()
    {
        $this->formattedData = new stdClass();
        $this->rawData = new stdClass;
        $this->unexpectedData = new stdClass;
    }

    public function getVersion(): string
    {
        return $this->formattedData->version ?? '4.0';
    }
}
