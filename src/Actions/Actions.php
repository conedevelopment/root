<?php

namespace Cone\Root\Actions;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @mixin \Illuminate\Support\Collection
 */
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
     * Register the given actions.
     */
    public function register(array|Action $actions): static
    {
        foreach (Arr::wrap($actions) as $action) {
            $this->actions->push($action);
        }

        return $this;
    }

    /**
     * Map the action to table components.
     */
    public function mapToTableComponents(Request $request): array
    {
        return $this->actions->map->toTableComponent($request)->all();
    }

    /**
     * Handle the dynamic method call.
     */
    public function __call($method, $parameters): mixed
    {
        return $this->forwardCallTo($this->actions, $method, $parameters);
    }
}
