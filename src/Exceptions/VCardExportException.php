<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Exceptions;

class VCardExportException extends AbstractVCardException
{
    public static function unableToWrite(string $filePath): self
    {
        return new self(sprintf("File %s doesn't exists or isn't writeable", $filePath));
    }
}
