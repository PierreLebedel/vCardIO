<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Models;

class VCardV40 extends AbstractVCard
{
    public string $version = '4.0';

    public $anniversary = null;

    public $calendarUserUri = null;

    public $calendarUri = null;

    public $categories = null;

    public $clientpidmap = null;

    public $fburl = null;

    public $gender = null;

    public $impp = null;

    public $kind = null;

    public $langs = null;

    public $member = null;

    public $nickNames = null;

    public $prodid = null;

    public $related = null;

    public $source = null;

    public $xml = null;
}
