<?php

namespace Cone\Root\Fields;

use Illuminate\Contracts\Support\Arrayable;

abstract class Field implements Arrayable
{
    /**
     * Create a new field instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [];
    }
}
