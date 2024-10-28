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

    public $sourceName = null;

    public $nickNames = null;

    public $prodid = null;

    public $profile = null;

    public $sortString = null;

    public $source = null;
}
