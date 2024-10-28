<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Explanatory;

use Pleb\VCardIO\Fields\AbstractField;

class UidField extends AbstractField
{
    protected string $name = 'uid';

    protected ?string $alias = null;

    protected bool $multiple = false;

    public function __construct(public string $uid) {}

    public static function make(string $value, array $attributes = []): self
    {
        if (strpos($value, 'urn:uuid:') === 0) {
            $value = substr($value, strlen('urn:uuid:'));
        }

        return new self($value);
    }

    public function render(): mixed
    {
        return $this->uid;
    }

    public function __toString(): string
    {
        return $this->toString('urn:uuid:'.$this->uid);
    }
}
