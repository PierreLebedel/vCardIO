<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields;

use DateTime;
use DateTimeInterface;
use Pleb\VCardIO\Exceptions\VCardParserException;

class BirthdayField extends AbstractField
{
    protected string $name = 'bday';

    protected ?string $alias = 'birthday';

    protected bool $multiple = false;

    public function __construct(public DateTimeInterface $dateTime) {
    }

    public static function make(string $value, array $attributes = []): self
    {
        $input = $value;
        if (substr($input, 0, 2) == '--') {
            $input = str_replace('--', date('Y'), $input);
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

        if(!$dateTime) {
            throw VCardParserException::unableToDecodeValue('datetime', $value);
        }

        $dateTime->setTime(0,0,0,0);

        return new self($dateTime);
    }

    public function render(): mixed
    {
        return $this->dateTime;
    }

    public function __toString(): string
    {
        return $this->toString($this->dateTime->format('Ymd'));
    }
}
