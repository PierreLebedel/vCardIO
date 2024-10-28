<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Identification;

use DateTime;
use DateTimeInterface;
use Pleb\VCardIO\Exceptions\VCardParserException;
use Pleb\VCardIO\Fields\AbstractField;

class BirthdayField extends AbstractField
{
    protected string $name = 'bday';

    protected ?string $alias = 'birthday';

    protected bool $multiple = false;

    public function __construct(public DateTimeInterface $dateTime, public bool $exactYear = true) {}

    public static function make(string $value, array $attributes = []): self
    {
        $exactYear = true;

        $input = $value;
        if (substr($input, 0, 2) == '--') {
            $input = str_replace('--', date('Y'), $input);
            $exactYear = false;
        }

        if (strlen($input) == 18 && (str_contains($input, '-') || str_contains($input, '+'))) {
            $dateTime = DateTime::createFromFormat('Ymd\THiO', $input);
        } elseif (strlen($input) == 16) {
            $dateTime = DateTime::createFromFormat('Ymd\THis\Z', $input);
        } elseif (strlen($input) == 10) {
            $dateTime = DateTime::createFromFormat('Y-m-d', $input);
        } elseif (strlen($input) == 8) {
            $dateTime = DateTime::createFromFormat('Ymd', $input);
        } elseif (strlen($input) == 6) {
            $dateTime = DateTime::createFromFormat('ymd', $input);
        }

        if (! $dateTime) {
            throw VCardParserException::unableToDecodeValue('datetime', $value);
        }

        $dateTime->setTime(0, 0, 0, 0);

        return new self($dateTime, $exactYear);
    }

    public function render(): mixed
    {
        return $this->dateTime;
    }

    public function __toString(): string
    {
        if (! $this->exactYear) {
            return $this->toString($this->dateTime->format('--md'));
        }

        return $this->toString($this->dateTime->format('Ymd'));
    }
}
