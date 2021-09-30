<?php

namespace Cone\Root\Contracts\Conversion;

use Closure;
use Cone\Root\Conversion\GdDriver;

interface Manager
{
    /**
     * Register a new conversion.
     *
     * @param  string  $name
     * @param  \Closure  $callback
     * @return void
     */
    public function registerConversion(string $name, Closure $callback): void;

    /**
     * Remove the given conversion.
     *
     * @param  string  $name
     * @return void
     */
    public function removeConversion(string $name): void;

    /**
     * Get all the registered conversions.
     *
     * @return array
     */
    public function getConversions(): array;

    /**
     * Create the GD driver.
     *
     * @return \Cone\Root\Conversion\GdDriver
     */
    public function createGdDriver(): GdDriver;
}
