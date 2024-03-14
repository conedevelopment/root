<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Actions\Action;
use Cone\Root\Actions\Actions;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

trait ResolvesActions
{
    /**
     * The actions collection.
     */
    protected ?Actions $actions = null;

    /**
     * The actions resolver callback.
     */
    protected ?Closure $actionsResolver = null;

    /**
     * Define the actions for the object.
     */
    public function actions(Request $request): array
    {
        return [];
    }

    /**
     * Set the actions resolver callback.
     */
    public function withActions(Closure $callback): static
    {
        $this->actionsResolver = $callback;

        return $this;
    }

    /**
     * Resolve the actions collection.
     */
    public function resolveActions(Request $request): Actions
    {
        if (is_null($this->actions)) {
            $this->actions = new Actions($this->actions($request));

            $this->actions->when(! is_null($this->actionsResolver), function (Actions $actions) use ($request): void {
                $actions->register(
                    Arr::wrap(call_user_func_array($this->actionsResolver, [$request]))
                );
            });

            $this->actions->each(function (Action $action) use ($request): void {
                $this->resolveAction($request, $action);
            });
        }

        return $this->actions;
    }

    /**
     * Handle the callback for the action resolution.
     */
    protected function resolveAction(Request $request, Action $action): void
    {
        //
    }
}
