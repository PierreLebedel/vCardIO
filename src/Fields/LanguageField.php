<?php

namespace Pleb\VCardIO\Fields;

use Pleb\VCardIO\Fields\AbstractField;
use stdClass;

class LanguageField extends AbstractField
{

    public function render() :mixed
    {
        $lang = substr(strtolower($this->value), 0, 2);

        $response = new stdClass();

        $response->value = $lang;

        if( $this->hasAttributes ){
            $response->attributes = $this->attributes;
        }

        return $response;
    }

}