<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

class VCardLogger
{
    protected array $logs = [];

    public function log(string $message)
    {
        $this->logs[] = $message;
    }

    public function getLogs(): array
    {
        return $this->logs;
    }
}
