<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Elements;

class VCardFloatElement extends VCardElement
{
    public function outputValue(): mixed
    {
        return floatval($this->inputValue);
    }
}
