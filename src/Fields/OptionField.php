<?php

namespace Pleb\VCardIO\Fields;

use Pleb\VCardIO\Fields\AbstractField;
use stdClass;

class OptionField extends AbstractField
{

    public array $structure = [];

    public ?string $default = null;

    public function setStructure(array $structure) :void
    {
        $this->structure = $structure;
    }

    public function setFormat(string $format) :void
    {
        $this->default = $format;
    }

    public function render() :mixed
    {
        $value = in_array(strtolower($this->value), array_map('strtolower', $this->structure)) ? strtolower($this->value) : $this->default;

        if(!$value){
            return null;
        }

        if( !$this->hasAttributes ){
            return $value;
        }

        $response = new stdClass();

        $response->value = $value;
        $response->attributes = $this->attributes;

        return $response;
    }

}