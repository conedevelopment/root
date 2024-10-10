<?php

namespace Cone\Root\Settings;

use Cone\Root\Interfaces\Settings\Repository as Contract;
use Cone\Root\Models\Setting;

class Repository implements Contract
{
    protected array $cache = [];

    public function __construct()
    {
        //
    }

    public function model(): Setting
    {
        return Setting::proxy();
    }

    public function get()
    {
        //
    }

    public function set()
    {
        //
    }

    public function delete()
    {
        //
    }

    public function cast(string $key, string $type)
    {
        //
    }
}
