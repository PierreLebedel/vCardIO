<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Geographical;

use DateTimeZone;
use Pleb\VCardIO\Exceptions\VCardParserException;
use Pleb\VCardIO\Fields\AbstractField;

class TimeZoneField extends AbstractField
{
    protected string $name = 'tz';

    protected ?string $alias = 'timeZone';

    protected bool $multiple = true;

    public function __construct(public DateTimeZone $timeZone) {}

    public static function make(string $value, array $attributes = []): self
    {
        $timeZone = new DateTimeZone($value) ?? null;

        if (! $timeZone) {
            throw VCardParserException::unableToDecodeValue('timezone', $value);
        }

        return new self($timeZone);
    }

    public static function getPossibleAttributes(): array
    {
        return [
            'pid',
            'pref',
            'mediatype',
            'type',
        ];
    }

    public function render(): mixed
    {
        return $this->timeZone;
    }

    public function __toString(): string
    {
        return $this->toString($this->timeZone->getName());
    }
}
