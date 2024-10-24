<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Elements;

class VCardUriElement extends VCardElement
{
    public function outputValue(): mixed
    {
        return $this->inputValue;
    }
}
