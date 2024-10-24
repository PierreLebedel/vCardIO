<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Elements;

use DateTime;
use stdClass;

class VCardDatetimeElement extends VCardElement
{
    public function outputValue(): mixed
    {
        $isYearExact = true;

        if (substr($this->inputValue, 0, 4) == '----') {
            $this->inputValue = str_replace('----', date('Y'), $this->inputValue);
            $isYearExact = false;
        } elseif (substr($this->inputValue, 0, 2) == '--') {
            $this->inputValue = str_replace('--', date('Y'), $this->inputValue);
            $isYearExact = false;
        }

        if (strlen($this->inputValue) == 10) {
            $datetime = DateTime::createFromFormat('Y-m-d', $this->inputValue);
        } elseif (strlen($this->inputValue) == 8) {
            $datetime = DateTime::createFromFormat('Ymd', $this->inputValue);
        } elseif (strlen($this->inputValue) == 6) {
            $datetime = DateTime::createFromFormat('ymd', $this->inputValue);
        }

        $object = new stdClass;
        $object->datetime = $datetime;
        $object->isYearExact = $isYearExact;

        return $object;
    }
}
