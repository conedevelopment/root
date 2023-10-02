<?php

namespace Cone\Root\Traits;

use Cone\Root\Actions\Action;
use Cone\Root\Actions\Actions;
use Illuminate\Http\Request;

trait ResolvesActions
{
    /**
     * The actions collection.
     */
    protected ?Actions $actions = null;

    /**
     * Define the actions for the object.
     */
    public function actions(Request $request): array
    {
        return [];
    }

    /**
     * Resolve the actions collection.
     */
    public function resolveActions(Request $request): Actions
    {
        if (is_null($this->actions)) {
            $this->actions = new Actions($this->actions($request));

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
