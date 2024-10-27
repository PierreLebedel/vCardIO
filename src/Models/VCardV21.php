<?php

namespace Pleb\VCardIO\Models;

class VCardV21 extends AbstractVCard
{

    public string $version = '2.1';

    public $agent = null;
    public $label = null;
    public $lang = null;
    public $mailer = null;

}
