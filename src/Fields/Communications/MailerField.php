<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Communications;

use Pleb\VCardIO\Fields\AbstractField;

class MailerField extends AbstractField
{
    protected string $name = 'mailer';

    protected ?string $alias = null;

    protected bool $multiple = true;

    public function __construct(public string $mailer) {}

    public static function make(string $value, array $attributes = []): self
    {
        return new self($value);
    }

    public function render(): mixed
    {
        return $this->mailer;
    }

    public function __toString(): string
    {
        return $this->toString($this->mailer);
    }
}
