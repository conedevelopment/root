<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Support\Collections\Actions;
use Illuminate\Http\Request;

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
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request): array
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
            $actions = static function (Request $request, Actions $collection) use ($actions): Actions {
                return $collection->merge($actions);
            };
        }

        $this->actionsResolver = $actions;

        return $this;
    }

    /**
     * Resolve the actions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Actions
     */
    public function resolveActions(Request $request): Actions
    {
        if (is_null($this->resolvedActions)) {
            $actions = Actions::make($this->actions($request));

            if (! is_null($this->actionsResolver)) {
                $actions = call_user_func_array($this->actionsResolver, [$request, $actions]);
            }

            $this->resolvedActions = $actions->each->mergeAuthorizationResolver(function (...$parameters): bool {
                return $this->authorized(...$parameters);
            });
        }

        return $this->resolvedActions;
    }
}
