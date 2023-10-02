<?php

namespace Cone\Root\Actions;

use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

class Actions
{
    use ForwardsCalls;

    /**
     * The actions collection.
     */
    protected Collection $actions;

    /**
     * Create a new actions instance.
     */
    public function __construct(array $actions = [])
    {
        $this->actions = new Collection($actions);
    }

    /**
     * Handle the dynamic method call.
     */
    public function __call($method, $parameters): mixed
    {
        return $this->forwardCallTo($this->actions, $method, $parameters);
    }
}
