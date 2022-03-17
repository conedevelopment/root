<?php

namespace Cone\Root\Interfaces\Support\Collections;

interface Assets
{
    /**
     * Register a new script.
     *
     * @param  string  $key
     * @param  string  $path
     * @return void
     */
    public function script(string $key, string $path): void;

    /**
     * Register a new style.
     *
     * @param  string  $key
     * @param  string  $path
     * @return void
     */
    public function style(string $key, string $path): void;
}
