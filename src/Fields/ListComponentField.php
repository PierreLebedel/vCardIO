<?php

namespace Pleb\VCardIO\Fields;

use Pleb\VCardIO\Fields\AbstractField;
use stdClass;

class ListComponentField extends AbstractField
{

    public array $structure = [];

    public function setStructure(array $structure) :void
    {
        $this->structure = $structure;
    }

    public function render() :mixed
    {
        $value = explode(',', $this->value)[0];

        $parts = explode(';', $value, count($this->structure));

        $response = new stdClass();

        foreach($this->structure as $k => $partName){
            $response->{$partName} = $parts[$k] ?? null;
        }

        if( $this->hasAttributes ){
            $response->attributes = $this->attributes;
        }

        return $response;
    }

}
