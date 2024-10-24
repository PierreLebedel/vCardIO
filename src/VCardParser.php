<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use DateTimeZone;
use Pleb\VCardIO\Elements\VCardElement;
use Pleb\VCardIO\Elements\VCardGeoElement;
use Pleb\VCardIO\Elements\VCardUriElement;
use Pleb\VCardIO\Elements\VCardFileElement;
use Pleb\VCardIO\Elements\VCardNameElement;
use Pleb\VCardIO\Elements\VCardFloatElement;
use Pleb\VCardIO\Elements\VCardAddressElement;
use Pleb\VCardIO\Elements\VCardDatetimeElement;
use Pleb\VCardIO\Elements\VCardMultipleElement;
use Pleb\VCardIO\Elements\VCardOrganizationElement;
use Pleb\VCardIO\Exceptions\VCardIOParserException;
use Pleb\VCardIO\Elements\VCardMultipleTypedElement;

class VCardParser
{
    protected string $rawData;

    protected VCardsCollection $vCards;

    protected ?VCard $currentVCard = null;

    protected ?VCard $currentVCardAgent = null;

    public function __construct(string $rawData)
    {
        $this->rawData = $rawData;
        $this->vCards = new VCardsCollection;
        $this->parse();
    }

    public static function parseFile(string $filePath): VCardsCollection
    {
        if (! file_exists($filePath)) {
            throw VCardIOParserException::fileNotFound($filePath);
        }
        if (! is_readable($filePath)) {
            throw VCardIOParserException::fileUnreadable($filePath);
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
            throw VCardIOParserException::invalidObjects('BEGIN:VCARD count differs of END:VCARD count');
        }

        $lines = explode("\n", $this->rawData);

        // Groups child line with its parent above
        foreach ($lines as $lineNumber => $lineContents) {
            $lineContents = trim($lineContents);
            if (empty($lineContents)) {
                continue;
            }
            if( !str_contains( $lineContents, ':' ) ){
                $previousLine = null;
                for( $i = ($lineNumber-1); $i >= 0; $i-- ){

                    if(array_key_exists($i, $lines) && is_null($previousLine)){
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

    private function getVCard(): VCard
    {
        if ($this->currentVCardAgent) {
            return $this->currentVCardAgent;
        }

        return $this->currentVCard;
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
        if (strtoupper($lineContents) == 'BEGIN:VCARD') {
            $this->currentVCard = new VCard;

            return;
        }

        if (strtoupper($lineContents) == 'AGENT:BEGIN:VCARD') {
            if (! $this->currentVCard) {
                throw VCardIOParserException::unexpectedLine($lineNumber, 'AGENT:BEGIN:VCARD');
            }

            $this->currentVCardAgent = new VCard;

            return;
        }

        if (strtoupper($lineContents) == 'END:VCARD') {
            if (! $this->currentVCard) {
                throw VCardIOParserException::unexpectedLine($lineNumber, 'END:VCARD');
            }

            if ($this->currentVCardAgent) {
                $this->currentVCard->agent = $this->currentVCardAgent;
                $this->currentVCardAgent = null;

                return;
            }

            $this->vCards->addVCard($this->currentVCard);
            $this->currentVCard = null;

            return;
        }

        if (! $this->currentVCard) {
            throw VCardIOParserException::unexpectedLine($lineNumber, $lineContents);
        }

        $lineContents = preg_replace("/\n(?:[ \t])/", '', $lineContents);
        $lineContents = preg_replace('/^\w+\./', '', $lineContents);
        //$lineContents = str_replace('-wrap');

        @[$name, $value] = explode(':', $lineContents, 2);
        if (empty($value)) {
            return;
        }

        $typesAll = explode(';', $name);
        $name = mb_strtolower($typesAll[0]);
        array_shift($typesAll);

        $types = [];
        if (! empty($typesAll)) {
            foreach ($typesAll as $type) {
                if (str_starts_with(strtolower($type), 'type=')) {
                    $subTypes = array_filter(explode(',', preg_replace('/^type=/i', '', $type)));
                    foreach ($subTypes as $subType) {
                        $typeOk = trim(strtolower($subType));
                        if (! empty($typeOk)) {
                            $types[] = $typeOk;
                        }
                    }
                }
                $type = preg_replace('/^type=/i', '', $type);

            }
        }

        $isRawValue = false;
        foreach ($types as $k => $type) {
            if (preg_match('/base64/', $type)) {
                $value = base64_decode($value);
                unset($types[$k]);
                $isRawValue = true;

            } elseif (preg_match('/encoding=b/', $type)) {
                $value = base64_decode($value);
                unset($types[$k]);
                $isRawValue = true;

            } elseif (preg_match('/quoted-printable/', $type)) {
                $value = quoted_printable_decode($value);
                unset($types[$k]);
                $isRawValue = true;

            } elseif (strpos($type, 'charset=') === 0) {
                try {
                    $value = mb_convert_encoding($value, 'UTF-8', substr($type, 8));
                } catch (\Exception $e) {
                    throw VCardIOParserException::invalidCharset($lineNumber, $type);
                }
                unset($types[$k]);
            }
        }

        $elementInstance = match ($name) {
            'adr'         => (new VCardAddressElement($value, $types)),
            'agent'       => (new VCardElement($value)),
            'anniversary' => (new VCardDatetimeElement($value)),
            'bday'        => (new VCardDatetimeElement($value)),
            'caladruri'   => (new VCardUriElement($value)),
            'caluri'      => (new VCardUriElement($value)),
            'categories'  => (new VCardMultipleElement($value)),
            'class'       => (new VCardElement($value)),
            // 'clientpidmap => ,
            'email' => (new VCardMultipleTypedElement($value, $types))->typed(['internet', 'x400', 'pref']),
            'fburl' => (new VCardUriElement($value)),
            'fn'    => (new VCardElement($value)),
            'gender' => (new VCardElement($value)),
            'geo' => (new VCardGeoElement($value)),
            'impp' => (new VCardMultipleTypedElement($value, $types))->typed(['personal', 'business', 'home', 'work', 'mobile', 'pref']),
            'key' => (new VCardElement($value)),
            //'kind' => ,
            'label' => (new VCardAddressElement($value, $types))->typed(['dom', 'intl', 'postal', 'parcel', 'home', 'work', 'pref']),
            //'lang' => ,
            'logo' => (new VCardFileElement($value, $types)),
            //'mailer' => ,
            //'member' => ,
            'n'        => (new VCardNameElement($value)),
            'nickname' => (new VCardMultipleElement($value)),
            //'note' => ,
            'org' => (new VCardOrganizationElement($value)),
            'photo' => (new VCardFileElement($value, $types)),
            //'prodid' => ,
            //'profile' => ,
            //'related' => ,
            //'rev' => ,
            //'role' => ,
            //'sort-string' => ,
            'sound' => (new VCardFileElement($value, $types)),
            //'source' => ,
            'tel' => (new VCardMultipleTypedElement($value, $types))->typed(['home', 'msg', 'work', 'pref', 'voice', 'fax', 'cell', 'video', 'pager', 'bbs', 'modem', 'car', 'isdn', 'pcs']),
            //'title' => ,
            //'tz' => ,
            //'uid' => ,
            //'url' => ,
            'version' => (new VCardFloatElement($value)),
            'xml'     => (new VCardElement($value)),
            default   => null,
        };

        if ($elementInstance) {
            if ($elementInstance->isMultiple()) {
                $this->getVCard()->{$name}[] = $elementInstance->outputValue();
            } else {
                $this->getVCard()->{$name} = $elementInstance->outputValue();
            }

            return;
        }

        $this->getVCard()->unparsedData[$name] = $value;

        //dump('no implementation for '.$name);
        return;

        dd('ici');

    }

    public function parseEmail(string $value, array $types = []): void
    {
        $this->getVCard()->addEmail($value, in_array('pref', $types), $types);
    }

    public function parsePhone(string $value, array $types = []): void
    {
        $this->getVCard()->addPhone($value, in_array('pref', $types), $types);
    }
}
