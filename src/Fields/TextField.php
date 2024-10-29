<?php

namespace Pleb\VCardIO\Fields;

use Pleb\VCardIO\Fields\AbstractField;
use stdClass;

class TextField extends AbstractField
{

    public function render() :mixed
    {
        if( !$this->hasAttributes ){
            return $this->value;
        }

        $response = new stdClass();

        $response->value = $this->value;
        $response->attributes = $this->attributes;

        return $response;
    }

}
