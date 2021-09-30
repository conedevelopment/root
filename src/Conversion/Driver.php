<?php

namespace Cone\Root\Conversion;

use Cone\Root\Models\Medium;

abstract class Driver
{
    /**
     * The driver config.
     *
     * @var array
     */
    protected array $config = [];

    /**
     * Create a new driver instance.
     *
     * @param  array  $config
     * @return void
     */
    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    /**
     * Perform the registered conversions on the medium.
     *
     * @param  \Cone\Root\Models\Medium  $medium
     * @return \Cone\Root\Models\Medium
     */
    abstract public function perform(Medium $medium): Medium;
}
