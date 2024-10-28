<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Organizational;

use Pleb\VCardIO\Fields\AbstractField;
use Pleb\VCardIO\Models\AbstractVCard;

class AgentField extends AbstractField
{
    protected string $name = 'agent';

    protected ?string $alias = null;

    protected bool $multiple = false;

    public function __construct(public string|AbstractVCard $agent) {
    }

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value);
    }

    public function render(): mixed
    {
        return $this->agent;
    }

    public function __toString(): string
    {
        return $this->toString((string) $this->agent);
    }
}
