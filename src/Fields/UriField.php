<?php

namespace Pleb\VCardIO\Fields;

use Pleb\VCardIO\Fields\AbstractField;
use stdClass;

class UriField extends AbstractField
{

    public function render() :mixed
    {

        if( empty($this->possibleAttributes) ){
            return $this->value;
        }

        $response = new stdClass();

        $response->value = $this->value;
        $response->attributes = $this->attributes;

        return $response;
    }

}
