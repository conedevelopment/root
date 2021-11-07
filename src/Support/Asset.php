<?php

namespace Cone\Root\Support;

use Illuminate\Contracts\Support\Arrayable;

class Asset implements Arrayable
{
    /**
     * Create a new controller instance.
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
