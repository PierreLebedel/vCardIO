<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Organizational;

use Pleb\VCardIO\Fields\AbstractField;
use Pleb\VCardIO\Models\AbstractVCard;

class AgentField extends AbstractField
{
    protected string $name = 'agent';

    protected ?string $alias = null;

    protected bool $multiple = true;

    public function __construct(public string|AbstractVCard $agent) {}

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
        if (is_string($this->agent)) {
            return $this->toString($this->agent);
        }

        $agentVCardArray = explode(PHP_EOL, (string) $this->agent);

        $agentVCardString = '';
        foreach ($agentVCardArray as $lineNumber => $lineContents) {
            if ($lineNumber > 0) {
                $lineContents = ' '.$lineContents;
            }
            $agentVCardString .= $lineContents.PHP_EOL;
        }

        return $this->toString(trim($agentVCardString));
    }
}
