<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Actions\Action;
use Cone\Root\Support\Collections\Actions;
use Illuminate\Http\Request;

trait ResolvesActions
{
    /**
     * The actions resolver callback.
     */
    protected ?Closure $actionsResolver = null;

    /**
     * The resolved actions.
     */
    protected ?Actions $actions = null;

    /**
     * Define the actions for the resource.
     */
    public function actions(Request $request): array
    {
        return [];
    }

    /**
     * Set the actions resolver.
     */
    public function withActions(array|Closure $actions): static
    {
        $this->actionsResolver = is_array($actions) ? fn (): array => $actions : $actions;

        return $this;
    }

    /**
     * Resolve the actions.
     */
    public function resolveActions(Request $request): Actions
    {
        if (is_null($this->actions)) {
            $this->actions = Actions::make($this->actions($request));

            if (! is_null($this->actionsResolver)) {
                $this->actions->push(...call_user_func_array($this->actionsResolver, [$request]));
            }

            $this->actions->each(function (Action $action) use ($request): void {
                $this->resolveAction($request, $action);
            });
        }

        return $this->actions;
    }

    /**
     * Handle the resolving event on the action instance.
     */
    protected function resolveAction(Request $request, Action $action): void
    {
        //
    }
}
