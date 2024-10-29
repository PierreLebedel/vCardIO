<?php
namespace Pleb\VCardIO\Enums;

use Pleb\VCardIO\Fields\XField;
use Pleb\VCardIO\Fields\SexField;
use Pleb\VCardIO\Fields\UriField;
use Pleb\VCardIO\Fields\TextField;
use Pleb\VCardIO\Fields\AgentField;
use Pleb\VCardIO\Fields\OptionField;
use Pleb\VCardIO\Fields\PidUriField;
use Pleb\VCardIO\Fields\DateTimeField;
use Pleb\VCardIO\Fields\LanguageField;
use Pleb\VCardIO\Fields\TextListField;
use Pleb\VCardIO\Fields\TimeZoneField;
use Pleb\VCardIO\Fields\ListComponentField;

enum VCardPropertyType: string
{
    case TEXT         = 'text';
    case URI          = 'uri';
    case OPTION       = 'option';
    case UNNAMED_LIST = 'text-list';
    case NAMED_LIST   = 'list-component';
    case PID_URI      = 'pid-uri';
    case DATETIME     = 'datetime';
    case TIMEZONE     = 'timezone';
    case LANGUAGE     = 'language-tag';
    case SEX          = 'sex';
    // case AGENT     = 'agent';
    case X            = 'x';

    public function getFieldClass() :string
    {
        return match ($this) {
            self::TEXT         => TextField::class,
            self::URI          => UriField::class,
            self::OPTION       => OptionField::class,
            self::UNNAMED_LIST => TextListField::class,
            self::NAMED_LIST   => ListComponentField::class,
            self::PID_URI      => PidUriField::class,
            self::DATETIME     => DateTimeField::class,
            self::TIMEZONE     => TimeZoneField::class,
            self::LANGUAGE     => LanguageField::class,
            self::SEX          => SexField::class,
            // self::AGENT        => AgentField::class,
            self::X            => XField::class,
            default            => TextField::class,
        };
    }

}
