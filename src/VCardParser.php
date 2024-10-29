<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use Pleb\VCardIO\Exceptions\VCardParserException;
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
                $this->currentVCardBuilder->setAgent($this->currentVCardAgentBuilder->get());
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

        [$name, $value, $attributes] = $this->extractData($lineContents);

        if (! $value) {
            dump('VCardParser empty value name:'.$name);

            return;
        }

        $property = $this->getVCardBuilder()->getProperty($name);

        if (! $property) {
            dump('VCardParser property not found name:'.$name);

            return;
        }

        if ($property->getName() == 'x') {
            $property->makeXField($name, $value, $attributes);

            return;
        }

        $property->makeField($value, $attributes);
    }

    protected function extractData($raw): array
    {
        @[$nameAttributes, $value] = explode(':', $raw, 2);

        $attributes = explode(';', $nameAttributes);
        $name = mb_strtolower($attributes[0]);
        array_shift($attributes);

        return [$name, $value, self::cleanAttributes($attributes)];
    }

    protected static function cleanAttributes(array $attributes): array
    {
        $attributes = array_filter($attributes);

        if (empty($attributes)) {
            return [];
        }

        //dump($attributes);

        foreach ($attributes as $k => $v) {

            if (is_numeric($k)) {

                $attrK = null;

                if (strpos($v, '=') != false) {
                    $attribute = explode('=', $v, 2);
                    if (count($attribute) == 2) {
                        $attrK = strtolower($attribute[0]);
                        $v = $attribute[1];
                    } elseif (count($attribute) == 1) {
                        $attrK = 'type';
                        $v = $attribute[0];
                    }
                }

                if (! $attrK) {
                    //dd('key not found in : '.$v);
                    $attrK = 'type';
                }

                //dump($attrK.':'.$v);

                unset($attributes[$k]);

                $values = explode(',', $v);

                foreach ($values as $value) {

                    if (strpos($value, '=') != false) {
                        $attribute = explode('=', $value, 2);
                        if (count($attribute) == 2) {
                            $value = $attribute[1];
                        }
                    }

                    if (array_key_exists(strtolower($attrK), $attributes)) {
                        if (is_array($attributes[strtolower($attrK)])) {
                            $previousValues = $attributes[strtolower($attrK)];
                        } else {
                            $previousValues = explode(',', $attributes[strtolower($attrK)]);
                        }

                        if (! in_array($value, $previousValues)) {
                            $previousValues[] = strtolower($value);
                        }

                        $attributes[strtolower($attrK)] = $previousValues;
                    } else {
                        $attributes[strtolower($attrK)] = strtolower($value);
                    }

                }
            }
        }

        foreach ($attributes as $k => $v) {
            if ($k == 'type') {
                if (is_array($v) && in_array('pref', $v)) {
                    $attributes['pref'] = 1;
                    $attributes['type'] = array_values(array_filter($v, function ($value) {
                        return ! in_array($value, ['pref']);
                    }));
                }
            }
        }

        //dump($attributes);
        //echo '<hr />';

        return $attributes;
    }

}
