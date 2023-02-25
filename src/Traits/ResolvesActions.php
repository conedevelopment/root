<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Actions\Action;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Support\Collections\Actions;

trait ResolvesActions
{
    /**
     * The actions resolver callback.
     */
    protected ?Closure $actionsResolver = null;

    /**
     * The resolved actions.
     */
    protected ?Actions $resolvedActions = null;

    /**
     * Define the actions for the resource.
     */
    public function actions(RootRequest $request): array
    {
        return [];
    }

    /**
     * Set the actions resolver.
     */
    public function withActions(array|Closure $actions): static
    {
        if (is_array($actions)) {
            $actions = static function (RootRequest $request, Actions $collection) use ($actions): Actions {
                return $collection->merge($actions);
            };
        }

        $this->actionsResolver = $actions;

        return $this;
    }

    /**
     * Resolve the actions.
     */
    public function resolveActions(RootRequest $request): Actions
    {
        if (is_null($this->resolvedActions)) {
            $actions = Actions::make($this->actions($request));

            if (! is_null($this->actionsResolver)) {
                $actions = call_user_func_array($this->actionsResolver, [$request, $actions]);
            }

            $this->resolvedActions = $actions->each(function (Action $action) use ($request): void {
                $this->resolveAction($request, $action);
            });
        }

        return $this->resolvedActions;
    }

    /**
     * Handle the resolving event on the action instance.
     */
    protected function resolveAction(RootRequest $request, Action $action): void
    {
        //
    }
}
