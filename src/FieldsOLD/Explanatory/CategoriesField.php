<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Explanatory;

use Pleb\VCardIO\Fields\AbstractField;
use Pleb\VCardIO\Formatters\TagsFormatter;
use Pleb\VCardIO\Formatters\ArrayFormatter;

class CategoriesField extends AbstractField
{
    protected string $name = 'categories';

    protected ?string $alias = null;

    protected bool $multiple = true;

    public function __construct(public array $categories, public array $attributes = [])
    {
        $this->categories = array_filter(array_map('trim', $this->categories));
    }

    public static function make(string $value, array $attributes = []): self
    {
        $categories = explode(',', $value);

        return new self($categories, $attributes);
    }

    public static function getDefaultValue(): mixed
    {
        return [];
    }

    public static function getPossibleAttributes(): array
    {
        return [
            'pid',
            'pref',
            'type',
            'altid',
        ];
    }

    public function render(): mixed
    {
        //return $this->categories;

        return new TagsFormatter($this->categories, $this->attributes);
    }

    public function __toString(): string
    {
        return $this->toString(implode(',', $this->categories), $this->attributes);
    }
}
