<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Table\Actions\Actions;

trait ResolvesActions
{
    /**
     * The resolved actions.
     */
    public readonly Actions $actions;

    /**
     * Define the actions for the resource.
     */
    public function actions(): array
    {
        return [];
    }

    /**
     * Apply the given callback on the actions.
     */
    public function withActions(Closure $callback): static
    {
        call_user_func_array($callback, [$this->actions, $this]);

        return $this;
    }
}
