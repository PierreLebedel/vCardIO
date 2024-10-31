<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields;

use stdClass;

class UriField extends AbstractField
{
    public string $format = 'uri';

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function render(): mixed
    {
        if (! $this->hasAttributes) {
            return $this->value;
        }

        $response = new stdClass;

        $response->value = $this->value;

        $formattedResponse = null;

        if ($this->format == 'geo') {
            $formattedResponse = $this->formatGeo($this->value);
        } elseif ($this->format == 'tel') {
            $formattedResponse = $this->formatTel($this->value);
        } elseif ($this->format == 'uuid') {
            $formattedResponse = $this->formatUuid($this->value);
        }

        $response->format = $this->format;
        $response->formatted = $formattedResponse;

        $response->attributes = $this->attributes;

        return $response;
    }

    public function relevantRender(): mixed
    {
        $response = $this->render();
        return ($response instanceof stdClass) ? $response->value : $response;
    }

    public function cleanValue(): string
    {
        if ($this->format == 'tel') {
            return $this->formatTel($this->value);
        }

        return $this->value;
    }

    public function formatGeo($input): ?stdClass
    {
        $formattedResponse = null;

        if (strpos($input, 'geo:') === 0) {
            $input = substr($input, 4);
        }

        if (strpos($input, ';') !== false) {
            $input = explode(';', $input)[0];
        }

        $coordinates = explode(',', $input, 2);

        if (count($coordinates) == 2) {
            $formattedResponse = new stdClass;
            $formattedResponse->latitude = (float) $coordinates[0];
            $formattedResponse->longitude = (float) $coordinates[1];
        }

        return $formattedResponse;
    }

    public function formatTel($input): string
    {
        if (strpos($input, 'tel:') === 0) {
            return $input;
        }

        return 'tel:'.$input;
    }

    public function formatUuid($input): ?string
    {
        if (strpos($input, 'urn:uuid:') === 0) {
            return $input;
        }

        if (strlen($input) == 36) {
            return 'urn:uuid:'.$input;
        }

        return null;
    }
}
