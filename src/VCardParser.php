<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

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
        $this->rawData = str_replace("=\n", '', $this->rawData);
        $this->rawData = str_replace(["\n ", "\n\t"], "\n", $this->rawData);

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
                //$this->currentVCardBuilder->agent($this->currentVCardAgentBuilder->get());
                dump('@todo agent');
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

        $this->getVCardBuilder()->addLine($lineContents);

    }
}
