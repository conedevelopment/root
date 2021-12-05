<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Actions\Action;
use Cone\Root\Exceptions\ActionResolutionException;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Actions extends Collection
{
    /**
     * Filter the actions that are available for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return static
     */
    public function available(Request $request): static
    {
        return $this->filter(static function (Action $action) use ($request): bool {
                        return $action->authorized($request) && $action->visible($request);
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
     * Call the resolved callbacks on the actions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  string  $key
     * @return void
     */
    public function resolved(Request $request, Resource $resource, string $key): void
    {
        $this->each(static function (Action $action) use ($request, $resource, $key): void {
            $action->resolved($request, $resource, sprintf('%s.actions.%s', $key, $action->getKey()));
        });
    }
}
