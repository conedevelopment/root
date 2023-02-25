<?php

namespace Cone\Root\Conversion;

use Closure;
use Cone\Root\Interfaces\Conversion\Manager as Contract;
use Illuminate\Support\Manager as BaseManager;

class Manager extends BaseManager implements Contract
{
    /**
     * The registered conversions.
     */
    protected array $conversions = [];

    /**
     * {@inheritdoc}
     */
    public function getDefaultDriver(): string
    {
        return $this->config->get('root.media.conversion.default');
    }

    /**
     * Register a new conversion.
     */
    public function registerConversion(string $name, Closure $callback): void
    {
        $this->conversions[$name] = $callback;
    }

    /**
     * Remove the given conversion.
     */
    public function removeConversion(string $name): void
    {
        unset($this->conversions[$name]);
    }

    /**
     * Get all the registered conversions.
     */
    public function getConversions(): array
    {
        return $this->conversions;
    }

    /**
     * Create the GD driver.
     *
     * @return \Cone\Root\Conversion\GdDriver
     */
    public function createGdDriver(): GdDriver
    {
        return new GdDriver(
            $this->config->get('root.media.conversion.drivers.gd', [])
        );
    }
}
