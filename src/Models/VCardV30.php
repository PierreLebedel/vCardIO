<?php

declare(strict_types=1);

namespace Pleb\VCardIO\Models;

class VCardV30 extends AbstractVCard
{
    public string $version = '3.0';

    public $agent = null;

    public $categories = null;

    public $class = null;

    public $impp = null;

    public $label = null;

    public $mailer = null;

    public $sourceName = null; // alias of name

    public $nickname = null;

    public $prodid = null;

    public $profile = null;

    public $sortString = null; // alias of sort-string

    public $source = null;
}
