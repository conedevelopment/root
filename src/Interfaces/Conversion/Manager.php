<?php

declare(strict_types=1);

namespace Cone\Root\Interfaces\Conversion;

use Closure;
use Cone\Root\Conversion\GdDriver;

interface Manager
{
    /**
     * Register a new conversion.
     */
    public function registerConversion(string $name, Closure $callback): void;

    /**
     * Remove the given conversion.
     */
    public function removeConversion(string $name): void;

    /**
     * Get all the registered conversions.
     */
    public function getConversions(): array;

    /**
     * Create the GD driver.
     */
    public function createGdDriver(): GdDriver;
}
