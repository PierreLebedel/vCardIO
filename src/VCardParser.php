<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use Pleb\VCardIO\VCardField;
use Pleb\VCardIO\VCardBuilder;
use Pleb\VCardIO\Fields\NameField;
use Pleb\VCardIO\VCardsCollection;
use Pleb\VCardIO\Fields\VersionField;
use Pleb\VCardIO\Fields\AbstractField;
use Pleb\VCardIO\Fields\FullNameField;
use Pleb\VCardIO\Enums\VCardVersionEnum;
use Pleb\VCardIO\Exceptions\VCardException;
use Pleb\VCardIO\Exceptions\VCardParserException;

class VCardParser
{
    protected string $rawData;

    protected VCardsCollection $vCards;

    protected ?VCardBuilder $currentVCardBuilder = null;

    protected ?VCardBuilder $currentVCardAgentBuilder = null;

    public function __construct(string $rawData)
    {
        $this->rawData = $rawData;
        $this->vCards = new VCardsCollection;
        $this->parse();
    }

    public static function parseFile(string $filePath): VCardsCollection
    {
        if (! file_exists($filePath)) {
            throw VCardParserException::fileNotFound($filePath);
        }
        if (! is_readable($filePath)) {
            throw VCardParserException::fileUnreadable($filePath);
        }

        return self::parseRaw(file_get_contents($filePath));
    }

    public static function parseRaw(string $rawData): VCardsCollection
    {
        $instance = new self($rawData);

        return $instance->vCards;
    }

    protected function parse()
    {
        $this->rawData = str_replace("\r", "\n", $this->rawData);
        $this->rawData = preg_replace('{(\n+)}', "\n", $this->rawData);
        $this->rawData = preg_replace('{(\n\s.+)=(\n)}', '$1-base64=-$2', $this->rawData);
        $this->rawData = str_replace("=\n", '', $this->rawData);
        $this->rawData = str_replace(["\n ", "\n\t"], "\n", $this->rawData);
        $this->rawData = str_replace("-base64=-\n", "=\n", $this->rawData);

        $beginEndMatches = [];
        $vCardBeginCount = preg_match_all('{BEGIN\:VCARD}miS', $this->rawData, $beginEndMatches);
        $vCardEndCount = preg_match_all('{END\:VCARD}miS', $this->rawData, $beginEndMatches);

        if (($vCardBeginCount != $vCardEndCount)) {
            throw VCardParserException::invalidObjects('BEGIN:VCARD count differs of END:VCARD count');
        }

        $lines = explode("\n", $this->rawData);

        // Groups child line with its parent above
        foreach ($lines as $lineNumber => $lineContents) {
            $lineContents = trim($lineContents);
            if (empty($lineContents)) {
                continue;
            }
            if (! str_contains($lineContents, ':')) {
                $previousLine = null;
                for ($i = ($lineNumber - 1); $i >= 0; $i--) {

                    if (array_key_exists($i, $lines) && is_null($previousLine)) {
                        $previousLine = $i;
                        break;
                    }
                }
                $lines[$previousLine] .= $lineContents;
            }
        }

        foreach ($lines as $lineNumber => $lineContents) {
            $this->parseLine($lineNumber, $lineContents);
        }
    }

    private function getVCardBuilder(): VCardBuilder
    {
        if ($this->currentVCardAgentBuilder) {
            return $this->currentVCardAgentBuilder;
        }

        return $this->currentVCardBuilder;
    }

    protected function fileElements(): array
    {
        return [
            'photo',
            'logo',
            'sound',
        ];
    }

    protected function parseLine(int $lineNumber, string $lineContents): void
    {
        $lineContents = preg_replace("/\n(?:[ \t])/", '', $lineContents);
        $lineContents = preg_replace('/^\w+\./', '', $lineContents);

        if (! $lineContents) {
            return;
        }

        if (strtoupper($lineContents) == 'BEGIN:VCARD') {
            $this->currentVCardBuilder = new VCardBuilder;

            return;
        }

        if (strtoupper($lineContents) == 'AGENT:BEGIN:VCARD') {
            if (! $this->currentVCardBuilder) {
                throw VCardParserException::unexpectedLine($lineNumber, 'AGENT:BEGIN:VCARD');
            }

            $this->currentVCardAgentBuilder = new VCardBuilder();

            return;
        }

        if (strtoupper($lineContents) == 'END:VCARD') {
            if (! $this->currentVCardBuilder) {
                throw VCardParserException::unexpectedLine($lineNumber, 'END:VCARD');
            }

            if ($this->currentVCardAgentBuilder) {
                //$this->currentVCardBuilder->setAgent($this->currentVCardAgentBuilder->get());
                $this->currentVCardAgentBuilder = null;

                return;
            }

            $this->vCards->addVCard($this->currentVCardBuilder->get());
            $this->currentVCardBuilder = null;

            return;
        }

        if (! $this->currentVCardBuilder) {
            throw VCardParserException::unexpectedLine($lineNumber, $lineContents);
        }

        $field = AbstractField::parse($lineContents);

        if(!$field){
            return;
            throw VCardParserException::unreadableDataLine($lineNumber);
        }

        if(!$this->currentVCardBuilder->getVersion()){
            if(!$field instanceof VersionField){
                throw VCardParserException::noVersionOnVCardStart($lineNumber);
            }

            $this->getVCardBuilder()->setVersion($field->versionEnum);
        }

        $this->getVCardBuilder()->addField($field);


        // match ($field->name) {
            // 'adr' => $field->assoc([
            //     'postOfficeAddress',
            //     'extendedAddress',
            //     'street',
            //     'locality',
            //     'region',
            //     'postalCode',
            //     'country',
            // ])->addAttribute('type', ['dom', 'intl', 'postal', 'parcel', 'home', 'work', 'pref'])->addAttribute('label'),
            // 'agent'        => $field->string(),
            // 'anniversary'  => $field->datetime(),
            // 'bday'         => $field->datetime(),
            // 'caladruri'    => $field->uri(),
            // 'caluri'       => $field->uri()->addAttribute('type'),
            // 'categories'   => $field->array()->addAttribute('type'),
            // 'class'        => $field->string(),
            // 'clientpidmap' => $field->assoc([
            //     'pid',
            //     'uri',
            // ]),
            // 'fburl'  => $field->uri()->addAttribute('type'),
            // 'gender' => $field->string(),
            // 'geo'    => $field->coordinates()->addAttribute('type'),
            // 'impp'   => $field->object()->addAttribute('type', ['personal', 'business', 'home', 'work', 'mobile', 'pref']),
            // 'key'    => $field->uri()->addAttribute('type'),
            // 'kind'   => $field->string()->in(['invividual', 'group', 'org', 'location']),
            // 'label'  => $field->assoc([
            //     'postOfficeAddress',
            //     'extendedAddress',
            //     'street',
            //     'locality',
            //     'region',
            //     'postalCode',
            //     'country',
            // ])->addAttribute('type', ['dom', 'intl', 'postal', 'parcel', 'home', 'work', 'pref']),
            // 'lang'   => $field->object()->addAttribute('type'),
            // 'logo'   => $field->uri()->addAttribute('type'),
            // 'mailer' => $field->string(),
            // 'member' => $field->uri(),
            // 'n'      => $field->assoc([
            //     'lastName',
            //     'firstName',
            //     'middleName',
            //     'namePrefix',
            //     'nameSuffix',
            // ]),
            // 'nickname' => $field->array()->addAttribute('type'),
            // 'note'     => $field->string()->addAttribute('type'),
            // 'org'      => $field->assoc([
            //     'name',
            //     'units1',
            //     'units2',
            // ])->addAttribute('type'),
            // 'photo'       => $field->uri()->addAttribute('type'),
            // 'prodid'      => $field->string(),
            // 'profile'     => $field->string(),
            // 'related'     => $field->uri()->addAttribute('type'),
            // 'rev'         => $field->datetime(),
            // 'role'        => $field->string()->addAttribute('type'),
            // 'sort-string' => $field->string(),
            // 'sound'       => $field->uri()->addAttribute('type'),
            // 'source'      => $field->uri(),
            // 'tel'         => $field->object()->addAttribute('type', ['home', 'msg', 'work', 'pref', 'voice', 'fax', 'cell', 'video', 'pager', 'bbs', 'modem', 'car', 'isdn', 'pcs']),
            // 'title'       => $field->string()->addAttribute('type'),
            // 'tz'          => $field->timezone()->addAttribute('type'),
            // 'uid'         => $field->string()->ltrim(['urn:uuid:']),
            // 'url'         => $field->uri()->addAttribute('type'),
            // 'xml'         => $field->string(),
            // default       => $field->unprocecced(),
        // };
    }

    public static function fields() :array
    {
        return [
            'adr'          => 'addresses',
            'agent'        => 'agent',
            'anniversary'  => 'anniversary',
            'bday'         => 'birthday',
            'caladruri'    => 'caladruri',
            'caluri'       => 'caluri',
            'categories'   => 'categories',
            'class'        => 'class',
            'clientpidmap' => 'clientpidmap',
            'email'        => 'emails',
            'fburl'        => 'fburl',
            'fn'           => FullNameField::class,
            'gender'       => 'gender',
            'geo'          => 'geo',
            'impp'         => 'impp',
            'key'          => 'key',
            'kind'         => 'kind',
            'label'        => 'label',
            'lang'         => 'lang',
            'langs'        => 'langs',
            'logo'         => 'logo',
            'mailer'       => 'mailer',
            'member'       => 'member',
            'n'            => NameField::class,
            'name'         => 'name',
            'nickname'     => 'nicknames',
            'nicknames'    => 'nicknames',
            'note'         => 'note',
            'org'          => 'organization',
            'photo'        => 'photo',
            'prodid'       => 'prodid',
            'profile'      => 'profile',
            'related'      => 'related',
            'rev'          => 'revision',
            'role'         => 'role',
            'sort-string'  => 'sort-string',
            'sound'        => 'sound',
            'source'       => 'source',
            'tel'          => 'phones',
            'title'        => 'title',
            'tz'           => 'timezone',
            'uid'          => 'uid',
            'url'          => 'url',
            'version'      => VersionField::class,
            'xml'          => 'xml',
        ];
    }

}
