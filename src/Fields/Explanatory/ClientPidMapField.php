<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Explanatory;

use Pleb\VCardIO\Exceptions\VCardParserException;
use Pleb\VCardIO\Fields\AbstractField;
use stdClass;

class ClientPidMapField extends AbstractField
{
    protected string $name = 'clientpidmap';

    protected ?string $alias = null;

    protected bool $multiple = true;

    public function __construct(public int $pid, public string $uri) {}

    public static function make(string $value, array $attributes = []): self
    {
        $parts = explode(';', $value, 2);

        if (count($parts) != 2) {
            throw VCardParserException::unableToDecodeValue('Client PID Map', $value);
        }

        if (! is_numeric($parts[0])) {
            throw VCardParserException::unableToDecodeValue('Client PID Map', $value);
        }

        return new self(intval($parts[0]), $parts[1]);
    }

    public function render(): mixed
    {
        $object = new stdClass;
        $object->pid = $this->pid;
        $object->uri = $this->uri;

        return $object;
    }

    public function __toString(): string
    {
        return $this->toString(implode(';', [$this->pid, $this->uri]));
    }
}
