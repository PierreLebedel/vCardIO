<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Explanatory;

use Pleb\VCardIO\Fields\AbstractField;

class CategoriesField extends AbstractField
{
    protected string $name = 'categories';

    protected ?string $alias = null;

    protected bool $multiple = false;

    public function __construct(public array $categories) {}

    public static function make(string $value, array $attributes = []): self
    {
        $categories = explode(',', $value);

        return new self($categories);
    }

    public static function getDefaultValue(): mixed
    {
        return [];
    }

    public function render(): mixed
    {
        return $this->categories;
    }

    public function __toString(): string
    {

        return $this->toString(implode(',', $this->categories));
    }
}
