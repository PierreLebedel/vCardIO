<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Elements;

class VCardMultipleElement extends VCardElement
{
    public function outputValue(): mixed
    {
        return explode(',', $this->inputValue);
    }
}
