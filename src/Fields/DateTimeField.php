<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields;

use DateTime;
use stdClass;

class DateTimeField extends AbstractField
{
    public ?DateTime $dateTime = null;

    public bool $exactYear = true;

    public ?string $format = null;

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function render(): mixed
    {
        $this->parse($this->value);

        $response = new stdClass;

        if ($this->dateTime && in_array($this->format, ['ymd', 'Ymd'])) {
            $this->dateTime->setTime(0, 0, 0, 0);
        }

        $response->dateTime = $this->dateTime;
        $response->format = $this->format;
        $response->formatted = null;
        $response->extactYear = $this->exactYear;

        if ($response->dateTime && $this->format) {

            if (in_array($this->format, ['ymd', 'Ymd']) && ! $this->exactYear) {
                $response->formatted = $this->dateTime->format('--md');

            } else {
                $response->formatted = $this->dateTime->format($this->format);
            }
        }

        if ($this->hasAttributes) {
            $response->attributes = $this->attributes;
        }

        return $response;
    }

    public function parse(string $input): void
    {
        $dateTime = null;
        $exactYear = true;

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

        $this->dateTime = $dateTime;

        $this->exactYear = $exactYear;
    }
}
