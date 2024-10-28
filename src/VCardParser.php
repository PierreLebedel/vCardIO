<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use Pleb\VCardIO\Exceptions\VCardParserException;
use Pleb\VCardIO\Fields\AbstractField;
use Pleb\VCardIO\Fields\Calendar;
use Pleb\VCardIO\Fields\Communications;
use Pleb\VCardIO\Fields\DeliveryAddressing;
use Pleb\VCardIO\Fields\Explanatory;
use Pleb\VCardIO\Fields\General;
use Pleb\VCardIO\Fields\Geographical;
use Pleb\VCardIO\Fields\Identification;
use Pleb\VCardIO\Fields\Organizational;
use Pleb\VCardIO\Fields\Security;

class VCardParser
{
    protected string $rawData;

    protected VCardsCollection $vCards;

    protected VCardLogger $logger;

    protected ?VCardBuilder $currentVCardBuilder = null;

    protected ?VCardBuilder $currentVCardAgentBuilder = null;

    public function __construct(string $rawData)
    {
        $this->rawData = $rawData;
        $this->vCards = new VCardsCollection;
        $this->logger = new VCardLogger;
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
                $this->logger->log('Empty line skipped: '.$lineNumber);

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
                unset($lines[$lineNumber]);
            }
        }

        foreach ($lines as $lineNumber => $lineContents) {
            $lineContents = preg_replace("/\n(?:[ \t])/", '', $lineContents);
            $lineContents = preg_replace('/^\w+\./', '', $lineContents);

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

    protected function parseLine(int $lineNumber, string $lineContents): void
    {
        if (! $lineContents) {
            $this->logger->log('Empty line skipped: '.$lineNumber);

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

            $this->currentVCardAgentBuilder = new VCardBuilder;

            return;
        }

        if (strtoupper($lineContents) == 'END:VCARD') {
            if (! $this->currentVCardBuilder) {
                throw VCardParserException::unexpectedLine($lineNumber, 'END:VCARD');
            }

            if ($this->currentVCardAgentBuilder) {
                $this->currentVCardBuilder->agent($this->currentVCardAgentBuilder->get());
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

        $field = AbstractField::makeFromRaw($lineContents);

        if (! $field) {
            $this->logger->log('No field found for raw: '.$lineContents);

            return;

            throw VCardParserException::unreadableDataLine($lineNumber);
        }

        if (! $this->currentVCardBuilder->getVersion()) {
            if (! $field instanceof Explanatory\VersionField) {
                throw VCardParserException::noVersionOnVCardStart($lineNumber);
            }

            $this->getVCardBuilder()->setVersion($field->versionEnum);
        }

        $this->getVCardBuilder()->addField($field);

        // match ($field->name) {
        // 'caladruri'    => $field->uri(),
        // 'caluri'       => $field->uri()->addAttribute('type'),
        // 'class'        => $field->string(),
        // 'fburl'  => $field->uri()->addAttribute('type'),
        // 'key'    => $field->uri()->addAttribute('type'),
        // 'label'  => $field->assoc([
        //     'postOfficeAddress',
        //     'extendedAddress',
        //     'street',
        //     'locality',
        //     'region',
        //     'postalCode',
        //     'country',
        // ])->addAttribute('type', ['dom', 'intl', 'postal', 'parcel', 'home', 'work', 'pref']),
        // 'mailer' => $field->string(),
        // 'prodid'      => $field->string(),
        // 'profile'     => $field->string(),
        // 'sort-string' => $field->string(),
        // default       => $field->unprocecced(),
        // };
    }

    public static function fieldsMap(): array
    {
        return [
            'adr'          => DeliveryAddressing\AddressField::class,
            'agent'        => Organizational\AgentField::class,
            'anniversary'  => Identification\AnniversaryField::class,
            'bday'         => Identification\BirthdayField::class,
            'caladruri'    => Calendar\CalUriField::class,
            'caluri'       => Calendar\CalAdrUriField::class,
            'categories'   => Explanatory\CategoriesField::class,
            'class'        => Security\ClassField::class,
            'clientpidmap' => Explanatory\ClientPidMapField::class,
            'email'        => Communications\EmailField::class,
            'fburl'        => Calendar\FbUrlField::class,
            'fn'           => Identification\FullNameField::class,
            'gender'       => Identification\GenderField::class,
            'geo'          => Geographical\GeoField::class,
            'impp'         => Communications\ImppField::class,
            'key'          => Security\KeyField::class,
            'kind'         => General\KindField::class,
            'label'        => DeliveryAddressing\LabelField::class,
            'lang'         => Communications\LangField::class,
            'logo'         => Organizational\LogoField::class,
            'mailer'       => Communications\MailerField::class,
            'member'       => Organizational\MemberField::class,
            'n'            => Identification\NameField::class,
            'name'         => General\SourceNameField::class,
            'nickname'     => Identification\NickNameField::class,
            'note'         => Explanatory\NoteField::class,
            'org'          => Organizational\OrganizationField::class,
            'photo'        => Identification\PhotoField::class,
            'prodid'       => Explanatory\ProdidField::class,
            'profile'      => General\ProfileField::class,
            'related'      => Organizational\RelatedField::class,
            'rev'          => Explanatory\RevField::class,
            'role'         => Organizational\RoleField::class,
            'sort-string'  => Explanatory\SortStringField::class,
            'sound'        => Explanatory\SoundField::class,
            'source'       => General\SourceField::class,
            'tel'          => Communications\PhoneField::class,
            'title'        => Organizational\TitleField::class,
            'tz'           => Geographical\TimeZoneField::class,
            'uid'          => Explanatory\UidField::class,
            'url'          => Explanatory\UrlField::class,
            'version'      => Explanatory\VersionField::class,
            'xml'          => General\XmlField::class,
        ];
    }
}
