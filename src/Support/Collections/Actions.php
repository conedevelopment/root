<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Actions\Action;
use Cone\Root\Exceptions\ActionResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class Actions extends Collection
{
    /**
     * Filter the actions that are visible for the given request and action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $action
     * @return static
     */
    public function filterVisible(Request $request, ?string $action = null): static
    {
        return $this->filter(static function (Action $item) use ($request, $action): bool {
                        return $item->visible($request, $action);
                    })
                    ->values();
    }

    /**
     * Resolve the action by its key.
     *
     * @param  string  $key
     * @return \Cone\Root\Actions\Action
     *
     * @throws \Cone\Root\Exceptions\ActionResolutionException
     */
    public function resolve(string $key): Action
    {
        $action = $this->first(static function (Action $action) use ($key): bool {
            return $action->getKey() === $key;
        });

        if (is_null($action)) {
            throw new ActionResolutionException("Unable to resolve action with key [{$key}].");
        }

        return $action;
    }

    /**
     * Resolve the action using the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Actions\Action
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function resolveFromRequest(Request $request): Action
    {
        try {
            return $this->resolve($request->input(Action::PARAMETER_NAME));
        } catch (ActionResolutionException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }
}
