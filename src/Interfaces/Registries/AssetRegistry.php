<?php

namespace Cone\Root\Interfaces\Registries;

interface AssetRegistry
{
    public function script(): void;

    /**
     * Register a new script.
     *
     * @param  string  $key
     * @param  string  $path
     * @return void
     */
    public function style(): void;
}
