<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use Pleb\VCardIO\Enums\VCardPropertyCardinality;
use Pleb\VCardIO\Enums\VCardPropertyType;
use Pleb\VCardIO\Fields\AbstractField;
use Pleb\VCardIO\Fields\XField;
use Pleb\VCardIO\Models\AbstractVCard;
use stdClass;

class VCardProperty
{
    public ?string $alias = null;

    public bool $hasAttributes = false;

    public array $possibleAttributes = [];

    public array $defaultAttributes = [];

    public array $structure = [];

    public string $format = '';

    public array $fields = [];

    public function __construct(
        public string $name,
        public VCardPropertyType $type,
        public VCardPropertyCardinality $cardinality,
        public VCardPropertyCardinality $relevantCardinality
    ) {}

    public static function make(string $name, string $type, string $cardinality, ?string $relevantCardinality = null): self
    {
        $name = strtolower($name);
        $typeEnum = VCardPropertyType::from($type);
        $cardinalityEnum = VCardPropertyCardinality::from($cardinality);
        $relevantCardinalityEnum = (is_null($relevantCardinality))
            ? VCardPropertyCardinality::from($cardinality)
            : VCardPropertyCardinality::from($relevantCardinality);

        return new self($name, $typeEnum, $cardinalityEnum, $relevantCardinalityEnum);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setAlias(string $alias): self
    {
        $this->alias = $alias;

        return $this;
    }

    public function getAlias(): string
    {
        return $this->alias ?? $this->name;
    }

    public function withAttr(): self
    {
        $this->hasAttributes = true;

        return $this;
    }

    public function addAttr(string $paramName, string $paramValues = '', ?string $default = null): self
    {
        $this->withAttr();

        $this->possibleAttributes[$paramName] = explode(',', $paramValues);

        if (! empty($default)) {
            $this->defaultAttributes[$paramName] = $default;
        }

        return $this;
    }

    public function addAttrs(string $names): self
    {
        $params = explode(',', $names);
        foreach ($params as $name) {
            $this->addAttr($name);
        }

        return $this;
    }

    public function setStructure(array $structure): self
    {
        $this->structure = $structure;

        return $this;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    public static function find(string $name): ?self
    {
        return match (strtolower($name)) {
            // General Properties
            'source'  => self::make($name, 'uri', '*', '*1')->addAttrs('pid,pref,altid,mediatype'),
            'kind'    => self::make($name, 'option', '*1')->withAttr()->setStructure(['individual', 'group', 'org', 'location'])->setFormat('individual'),
            'xml'     => self::make($name, 'text', '*', '*1'),
            'name'    => self::make($name, 'text', '*', '*1')->setAlias('sourceName'),
            'profile' => self::make($name, 'text', '*1'),
            // Identification Properties
            'fn'          => self::make($name, 'text', '1*', '1')->addAttrs('type,language,altid,pid,pref'),
            'n'           => self::make($name, 'list-component', '*1')->addAttrs('sort-as,language,altid')->setStructure(['lastName', 'firstName', 'middleName', 'namePrefix', 'nameSuffix']),
            'nickname'    => self::make($name, 'text-list', '*', '*1')->addAttrs('type,language,altid,pid,pref'),
            'photo'       => self::make($name, 'uri', '*', '*1')->addAttrs('type,mediatype,altid,pid,pref'),
            'bday'        => self::make($name, 'datetime', '*1')->addAttrs('altid,calscale')->setFormat('Ymd'),
            'anniversary' => self::make($name, 'datetime', '*1')->addAttrs('altid,calscale'),
            'gender'      => self::make($name, 'sex', '*1')->withAttr(),
            // Delivery Addressing Properties
            'adr'   => self::make($name, 'list-component', '*')->addAttrs('label,language,geo,tz,altid,pid,pref,type')->setStructure(['postOfficeAddress', 'extendedAddress', 'street', 'locality', 'region', 'postalCode', 'country']),
            'label' => self::make($name, 'list-component', '*')->addAttrs('label,language,geo,tz,altid,pid,pref,type')->setStructure(['postOfficeAddress', 'extendedAddress', 'street', 'locality', 'region', 'postalCode', 'country']),
            // Communications Properties
            'tel'    => self::make($name, 'uri', '*')->addAttrs('pid,pref,altid')->addAttr('type', 'text,voice,fax,cell,video,pager,textphone', 'voice')->setFormat('tel'),
            'email'  => self::make($name, 'text', '*')->addAttrs('pid,pref,type,altid'),
            'impp'   => self::make($name, 'uri', '*')->addAttrs('pid,pref,type,mediatype,altid'),
            'lang'   => self::make($name, 'language-tag', '*')->addAttrs('pid,pref,type,altid'),
            'mailer' => self::make($name, 'text', '*1'),
            // Geographical Properties
            'tz'  => self::make($name, 'timezone', '*', '*1')->addAttrs('pid,pref,type,altid,mediatype'),
            'geo' => self::make($name, 'uri', '*', '*1')->addAttrs('pid,pref,type,altid,mediatype')->setFormat('geo'),
            // Organizational Properties
            'title'   => self::make($name, 'text', '*', '*1')->addAttrs('language,pid,pref,type,altid'),
            'role'    => self::make($name, 'text', '*', '*1')->addAttrs('language,pid,pref,type,altid'),
            'logo'    => self::make($name, 'uri', '*', '*1')->addAttrs('language,pid,pref,type,mediatype,altid'),
            'org'     => self::make($name, 'list-component', '*', '*1')->addAttrs('sort-as,language,pid,pref,type,altid')->setStructure(['organizationName', 'unit1', 'unit2']),
            'member'  => self::make($name, 'uri', '*', '*1')->addAttrs('pid,pref,mediatype,altid'),
            'related' => self::make($name, 'uri', '*', '*1')->addAttrs('pid,pref,altid')->addAttr('type', 'contact,acquaintance,friend,met,co-worker,colleague,co-resident,neighbor,child,parent,sibling,spouse,kin,muse,crush,date,sweetheart,me,agent,emergency'),
            'agent'   => self::make($name, 'uri', '*1'),
            // Explanatory Properties
            'categories'   => self::make($name, 'text-list', '*', '*1')->addAttrs('pid,pref,type,altid'),
            'note'         => self::make($name, 'text', '*', '*1')->addAttrs('language,pid,pref,type,altid'),
            'prodid'       => self::make($name, 'text', '*1')->withAttr(),
            'rev'          => self::make($name, 'datetime', '*1')->withAttr()->setFormat('Ymd\THis\Z'),
            'sound'        => self::make($name, 'uri', '*', '*1')->addAttrs('language,pid,pref,type,mediatype,altid'),
            'uid'          => self::make($name, 'uri', '*1')->withAttr()->setFormat('uuid'),
            'clientpidmap' => self::make($name, 'pid-uri', '*')->withAttr(),
            'url'          => self::make($name, 'uri', '*', '*1')->addAttrs('pid,pref,type,mediatype,altid')->setFormat('url'),
            'version'      => self::make($name, 'text', '1'),
            'sort-string'  => self::make($name, 'text', '*1')->setAlias('sortString'),
            // Security Properties
            'key'   => self::make($name, 'uri', '*', '*1')->addAttrs('pid,pref,type,altid'),
            'class' => self::make($name, 'text', '*1'),
            // Calendar Properties
            'fburl'     => self::make($name, 'uri', '*', '*1')->addAttrs('pid,pref,type,mediatype,altid'),
            'caladruri' => self::make($name, 'uri', '*', '*1')->addAttrs('pid,pref,type,mediatype,altid'),
            'caluri'    => self::make($name, 'uri', '*', '*1')->addAttrs('pid,pref,type,mediatype,altid'),
            // Extended Properties
            'x'     => self::make($name, 'x', '*')->withAttr(),
            default => null,
        };
    }

    public function addField(AbstractField $field): self
    {
        if (! $this->cardinality->isMultiple()) {
            $this->fields = [];
        }

        $field->setHasAttributes($this->hasAttributes);

        if (! empty($this->possibleAttributes)) {
            $field->setPossibleAttributes($this->possibleAttributes);
            $field->setDefaultAttributes($this->defaultAttributes);
        }

        if (! empty($this->structure)) {
            if (method_exists($field, 'setStructure')) {
                $field->setStructure($this->structure);
            }
        }

        if (! empty($this->format)) {
            if (method_exists($field, 'setFormat')) {
                $field->setFormat($this->format);
            }
        }

        //dump($this->getName(),$field);

        $this->fields[] = $field;

        return $this;
    }

    public function makeField(string $value, array $attributes = []): AbstractField
    {
        $fieldClass = $this->type->getFieldClass();

        if (! $fieldClass) {
            //dump('VCardProperty->addField() : fieldClass not found type:'.$this->type->value);

            return $this;
        }

        $field = new $fieldClass($value, $attributes);

        $this->addField($field);

        return $field;
    }

    public function makeXField(string $name, string $value, array $attributes = []): self
    {
        $field = new XField($value, $attributes);
        $field->setFormat($name);

        return $this->addField($field);
    }

    public function getFields(): array
    {
        return $this->fields;
    }

    public function apply(AbstractVCard $vCard): AbstractVCard
    {
        if (empty($this->fields)) {
            return $vCard;
        }

        $values = null;

        $releventValues = null;

        foreach ($this->fields as $field) {
            if ($this->cardinality->isMultiple()) {
                if (! is_array($values)) {
                    $values = [];
                }
                $values[] = $field->render();
            } else {
                $values = $field->render();
            }

            if ($this->getName() == 'x') {
                if (! $releventValues instanceof stdClass) {
                    $releventValues = new stdClass;
                }
                $xName = $field->formattedName();
                if (! property_exists($releventValues, $xName)) {
                    $releventValues->{$xName} = $field->relevantRender();
                } else {
                    if (! is_array($releventValues->{$xName})) {
                        $previousValue = $releventValues->{$xName};
                        $releventValues->{$xName} = [$previousValue];
                    }
                    $releventValues->{$xName}[] = $field->relevantRender();
                }

            } elseif ($this->relevantCardinality->isMultiple()) {
                if (! is_array($values)) {
                    $releventValues = [];
                }
                $releventValues[] = $field->relevantRender();
            } else {
                $releventValues = $field->relevantRender();
            }
        }

        if (! property_exists($vCard, $this->getAlias())) {
            //dump('VCardProperty->apply() : property:'.$this->getAlias().' not found in class '.basename(get_class($vCard)));

            return $vCard;
        }

        $vCard->{$this->getAlias()} = $values;

        $vCard->relevantData->{$this->getAlias()} = $releventValues;

        return $vCard;
    }

    public function __toString(): string
    {
        if (empty($this->fields)) {
            return '';
        }

        $propertyString = '';

        foreach ($this->fields as $field) {
            if ($field instanceof XField) {
                $propertyString .= strtoupper($this->getName().'-'.$field->name).(string) $field.PHP_EOL;
            } else {
                $propertyString .= strtoupper($this->getName()).(string) $field.PHP_EOL;
            }
        }

        return trim($propertyString);
    }
}
