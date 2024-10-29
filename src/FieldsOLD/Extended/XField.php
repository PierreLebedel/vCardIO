<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Fields\Extended;

use Pleb\VCardIO\Exceptions\VCardBuilderException;
use Pleb\VCardIO\Fields\AbstractField;
use Pleb\VCardIO\Models\AbstractVCard;
use stdClass;

class XField extends AbstractField
{
    protected string $name = 'x';

    protected bool $multiple = true;

    public function __construct(public string $xname, public string $xvalue = '', public array $attributes = []) {}

    public static function make(string $value, array $attributes = []): self
    {
        throw VCardBuilderException::cantMakeXField();
    }

    public static function makeX(string $name, string $value, array $attributes = []): self
    {
        if (strpos($name, 'x-') === 0) {
            $name = substr($name, 2);
        }

        return new self($name, $value, $attributes);
    }

    protected function lowerCase($string)
    {
        return str_replace('-', '', strtolower($string));
    }

    public function apply(AbstractVCard $vCard): AbstractVCard
    {
        if (! is_object($vCard->{$this->getAlias()})) {
            $vCard->{$this->getAlias()} = new stdClass;
        }

        $vCard->{$this->getAlias()}->{$this->lowerCase($this->xname)} = $this->render();

        return $vCard;
    }

    public function render(): mixed
    {
        return $this->xvalue;
    }

    public function __toString(): string
    {
        $attributes = self::parseAttributes($this->attributes);

        $property = 'X-'.strtoupper($this->xname);

        if (! empty($attributes)) {
            foreach ($attributes as $k => $v) {
                if (empty($v)) {
                    continue;
                }
                $property .= ';'.strtoupper($k).'=';
                $property .= is_array($v) ? strtoupper(implode(',', array_values($v))) : strtoupper($v);
            }
        }

        $property .= ':'.$this->xvalue;

        return $property;
    }
}
