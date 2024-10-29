<?php

namespace Pleb\VCardIO;

use Pleb\VCardIO\Enums\VCardPropertyCardinality;
use Pleb\VCardIO\Enums\VCardPropertyType;
use Pleb\VCardIO\Fields\AbstractField;
use Pleb\VCardIO\Models\AbstractVCard;

class VCardProperty
{

    public ?string $alias = null;

    public array $possibleAttributes = [];

    public array $defaultAttributes = [];

    public array $fields = [];

    public function __construct(public string $name, public VCardPropertyType $type, public VCardPropertyCardinality $cardinality)
    {

    }

    public static function make(string $name, string $type, string $cardinality) :self
    {
        $name = strtolower($name);
        $typeEnum = VCardPropertyType::from($type);
        $cardinalityEnum = VCardPropertyCardinality::from($cardinality);

        return new self($name, $typeEnum, $cardinalityEnum);
    }

    public function getName() :string
    {
        return $this->name;
    }

    public function setAlias(string $alias) :self
    {
        $this->alias = $alias;
        return $this;
    }

    public function getAlias() :string
    {
        return $this->alias ?? $this->name;
    }

    public function addParam(string $paramName, string $paramValues = '', ?string $default = null) :self
    {
        $this->possibleAttributes[$paramName] = explode(',', $paramValues);

        if(!empty($default)){
            $this->defaultAttributes[$paramName] = $default;
        }

        return $this;
    }

    public function addParams(string $names) :self
    {
        $params = explode(',', $names);
        foreach($params as $name){
            $this->addParam($name);
        }
        return $this;
    }

    public function setRestriction(array $options) :self
    {

        return $this;
    }

    public function setStructure(array $structure) :self
    {

        return $this;
    }

    public static function find(string $name) :?self
    {
        return match (strtolower($name)) {
            // General Properties
            'source'       => self::make($name, 'uri', '*')->addParams('pid,pref,altid,mediatype'),
            'kind'         => self::make($name, 'option', '*1')->setRestriction(['individual', 'group', 'org', 'location']),
            'xml'          => self::make($name, 'text', '*'),
            // Identification Properties
            'fn'           => self::make($name, 'text', '1*')->addParams('type,language,altid,pid,pref'),
            'n'            => self::make($name, 'list-component', '*1')->addParams('sort-as,language,altid')->setStructure(['lastName','firstName','middleName','namePrefix','nameSuffix']),
            'nickname'     => self::make($name, 'text-list', '*')->addParams('type,language,altid,pid,pref'),
            'photo'        => self::make($name, 'uri', '*')->addParams('type,mediatype,altid,pid,pref'),
            'bday'         => self::make($name, 'datetime', '*1')->addParams('altid,calscale'),
            'anniversary'  => self::make($name, 'datetime', '*1')->addParams('altid,calscale'),
            'gender'       => self::make($name, 'sex', '*1'),
            // Delivery Addressing Properties
            'adr'          => self::make($name, 'list-component', '*')->addParams('label,language,geo,tz,altid,pid,pref,type')->setStructure(['postOfficeAddress','extendedAddress','street','locality','region','postalCode','country']),
            // Communications Properties
            'tel'          => self::make($name, 'text', '*')->addParams('pid,pref,altid')->addParam('type', 'text,voice,fax,cell,video,pager,textphone', 'voice'),
            'email'        => self::make($name, 'text', '*')->addParams('pid,pref,type,altid'),
            'impp'         => self::make($name, 'uri', '*')->addParams('pid,pref,type,mediatype,altid'),
            'lang'         => self::make($name, 'language-tag', '*')->addParams('pid,pref,type,altid'),
            // Geographical Properties
            'tz'           => self::make($name, 'text', '*')->addParams('pid,pref,type,altid,mediatype'),
            'geo'          => self::make($name, 'uri', '*')->addParams('pid,pref,type,altid,mediatype'),
            // Organizational Properties
            'title'        => self::make($name, 'text', '*')->addParams('language,pid,pref,type,altid'),
            'role'         => self::make($name, 'text', '*')->addParams('language,pid,pref,type,altid'),
            'logo'         => self::make($name, 'uri', '*')->addParams('language,pid,pref,type,mediatype,altid'),
            'org'          => self::make($name, 'list-component', '*')->addParams('sort-as,language,pid,pref,type,altid')->setStructure(['organizationName','unit1','unit2']),
            'member'       => self::make($name, 'uri', '*')->addParams('pid,pref,mediatype,altid'),
            'related'      => self::make($name, 'uri', '*')->addParams('pid,pref,altid')->addParam('type', 'contact,acquaintance,friend,met,co-worker,colleague,co-resident,neighbor,child,parent,sibling,spouse,kin,muse,crush,date,sweetheart,me,agent,emergency'),
            // Explanatory Properties
            'categories'   => self::make($name, 'text-list', '*')->addParams('pid,pref,type,altid'),
            'note'         => self::make($name, 'text', '*')->addParams('language,pid,pref,type,altid'),
            'prodid'       => self::make($name, 'text', '*1'),
            'rev'          => self::make($name, 'timestamp', '*1'),
            'sound'        => self::make($name, 'uri', '*')->addParams('language,pid,pref,type,mediatype,altid'),
            'uid'          => self::make($name, 'uri', '*1'),
            'clientpidmap' => self::make($name, 'list-component', '*')->setStructure(['pid','uri']),
            'url'          => self::make($name, 'uri', '*')->addParams('pid,pref,type,mediatype,altid'),
            'version'      => self::make($name, 'text', '1'),
            // Security Properties
            'key'          => self::make($name, 'uri', '*')->addParams('pid,pref,type,altid'),
            // Calendar Properties
            'fburl'        => self::make($name, 'uri', '*')->addParams('pid,pref,type,mediatype,altid'),
            'caladruri'    => self::make($name, 'uri', '*')->addParams('pid,pref,type,mediatype,altid'),
            'caluri'       => self::make($name, 'uri', '*')->addParams('pid,pref,type,mediatype,altid'),
            // Extended Properties

            'agent'        => self::make($name, 'text', '*'),
            'class'        => self::make($name, 'text', '*'),
            'label'        => self::make($name, 'text', '*'),
            'mailer'       => self::make($name, 'text', '*'),
            'name'         => self::make($name, 'text', '*')->setAlias('sourceName'),
            'profile'      => self::make($name, 'text', '*'),
            'sort-string'  => self::make($name, 'text', '*')->setAlias('sortString'),
            default => null,
        };
    }

    public function addField(AbstractField $field) :self
    {
        if( !$this->cardinality->isMultiple() ){
            $this->fields = [];
        }

        $this->fields[] = $field;

        return $this;
    }

    public function makeField(string $value, array $attributes = []) :self
    {
        $fieldClass = $this->type->getFieldClass();

        if(!$fieldClass){
            dump('VCardProperty->addField() : fieldClass not found type:'.$this->type->value);
            return $this;
        }

        $field = new $fieldClass($value, $attributes);

        if(!empty($this->possibleAttributes)){
            $field->setPossibleAttributes($this->possibleAttributes);
            $field->setDefaultAttributes($this->defaultAttributes);
        }

        return $this->addField($field);
    }

    public function apply(AbstractVCard $vCard) :AbstractVCard
    {
        if(empty($this->fields)){
            return $vCard;
        }

        $values = null;

        foreach($this->fields as $field){
            if( $this->cardinality->isMultiple() ){
                if(!is_array($values)){
                    $values = [];
                }
                $values[] = $field->render();
            }else{
                $values = $field->render();
            }
        }

        if(!property_exists($vCard, $this->getAlias())){
            dump('VCardProperty->apply() : property:'.$this->getAlias().' not found in class '.basename(get_class($vCard)));
            return $vCard;
        }

        $vCard->{$this->getAlias()} = $values;
        return $vCard;
    }

    public function __toString() :string
    {
        if(empty($this->fields)){
            return '';
        }

        $propertyString = '';

        foreach($this->fields as $field){
            $propertyString .= strtoupper($this->getName()).(string)$field;
        }

        return $propertyString;
    }

}
