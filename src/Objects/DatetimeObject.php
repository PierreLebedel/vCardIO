<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Objects;

class DatetimeObject
{
    public function __construct(public \DateTime $datetime, public bool $isYearExact = true) {}
}
