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
     *
     * @var \Closure|null
     */
    protected ?Closure $actionsResolver = null;

    /**
     * The resolved actions.
     *
     * @var \Cone\Root\Support\Collections\Actions|null
     */
    protected ?Actions $resolvedActions = null;

    /**
     * Define the actions for the resource.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function actions(RootRequest $request): array
    {
        return [];
    }

    /**
     * Set the actions resolver.
     *
     * @param  array|\Closure  $actions
     * @return $this
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
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Cone\Root\Support\Collections\Actions
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
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Cone\Root\Actions\Action  $action
     * @return void
     */
    protected function resolveAction(RootRequest $request, Action $action): void
    {
        //
    }
}
