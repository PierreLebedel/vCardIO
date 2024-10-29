<?php
namespace Pleb\VCardIO\Enums;

use Pleb\VCardIO\Fields\TextField;
use Pleb\VCardIO\Fields\UriField;

enum VCardPropertyType: string
{
    case TEXT         = 'text';
    case URI          = 'uri';
    case OPTION       = 'option';
    case UNNAMED_LIST = 'text-list';
    case NAMED_LIST   = 'list-component';
    case DATETIME     = 'datetime';
    case TIMESTAMP    = 'timestamp';
    case LANGUAGE     = 'language-tag';
    case SEX          = 'sex';

    public function getDescription() :string
    {
        return match ($this) {
            self::TEXT => 'A single text value.',
            // self::URI  => 'A single URI value.',
            // self::OPTION  => 'A single predefined text value.',
            // self::UNNAMED_LIST  => 'One or more text values separated by a comma character.',
            // self::NAMED_LIST  => 'A structured text value.',
            default    => 'Unknown property type.',
        };
    }

    public function getFieldClass() :string
    {
        return match ($this) {
            self::TEXT => TextField::class,
            self::URI => UriField::class,

            default => TextField::class,
        };
    }

}
