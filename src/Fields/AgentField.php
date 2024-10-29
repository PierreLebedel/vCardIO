<?php

namespace Pleb\VCardIO\Fields;

use Pleb\VCardIO\Fields\AbstractField;
use Pleb\VCardIO\Models\AbstractVCard;
use stdClass;

class AgentField extends AbstractField
{

    public ?AbstractVCard $agentVCard = null;

    public static function makeFromVCard(AbstractVCard $vCard)
    {
        $instance = new self('', []);

        $instance->agentVCard = $vCard;

        return $instance;
    }

    public function render() :mixed
    {
        if(!$this->agentVCard){
            return null;
        }

        return $this->agentVCard;
    }

    public function __toString(): string
    {
        if(!$this->agentVCard){
            return null;
        }

        $agentVCardArray = explode(PHP_EOL, (string) $this->agentVCard);

        $agentVCardString = ':';
        foreach ($agentVCardArray as $lineNumber => $lineContents) {
            if ($lineNumber > 0) {
                $lineContents = ' '.$lineContents;
            }
            $agentVCardString .= $lineContents.PHP_EOL;
        }

        return trim($agentVCardString);
    }

}
