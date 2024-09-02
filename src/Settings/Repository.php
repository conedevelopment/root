<?php

namespace Cone\Root\Settings;

use Cone\Root\Interfaces\Settings\Repository as Contract;

class Repository implements Contract
{
    protected array $cache = [];
    protected array $casts = [];

    public function __construct()
    {
        //
    }

    // get
    // set
    // delete
}
