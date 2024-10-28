<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Models;

use SimpleXMLElement;

class VCardV40 extends AbstractVCard
{
    public string $version = '4.0';

    public $anniversary = null;

    public $caladruri = null;

    public $caluri = null;

    public $categories = null;

    public $clientpidmap = null;

    public $fburl = null;

    public $gender = null;

    public $impp = null;

    public $kind = null;

    public array $langs = [];

    public $member = null;

    public array $nickNames = [];

    public $prodid = null;

    public $related = null;

    public $source = null;

    public ?string $xml = null;
}
