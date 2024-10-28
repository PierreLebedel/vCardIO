<?php

declare(strict_types=1);

namespace Pleb\VCardIO;

use Sabre\VObject\EofException;
use Sabre\VObject\ParseException;
use Sabre\VObject\Parser\MimeDir as SabreParserMimeDir;

class VCardReader extends SabreParserMimeDir
{

    protected function parseDocument()
    {
        $line = $this->readLine();

        // BOM is ZERO WIDTH NO-BREAK SPACE (U+FEFF).
        // It's 0xEF 0xBB 0xBF in UTF-8 hex.
        if (3 <= strlen($line)
            && 0xef === ord($line[0])
            && 0xbb === ord($line[1])
            && 0xbf === ord($line[2])) {
            $line = substr($line, 3);
        }

        switch (strtoupper($line)) {
            case 'BEGIN:VCARD':
                $class = VCard::$componentMap['VCARD'];
                break;
            default:
                throw new ParseException('This parser only supports VCARD files');
        }

        $this->root = new $class([], false);

        while (true) {
            // Reading until we hit END:
            try {
                $line = $this->readLine();
            } catch (EofException $oEx) {
                $line = 'END:'.$this->root->name;
            }
            if ('END:' === strtoupper(substr($line, 0, 4))) {
                break;
            }
            $result = $this->parseLine($line);
            if ($result) {
                $this->root->add($result);
            }
        }

        $name = strtoupper(substr($line, 4));
        if ($name !== $this->root->name) {
            throw new ParseException('Invalid MimeDir file. expected: "END:'.$this->root->name.'" got: "END:'.$name.'"');
        }
    }

}
