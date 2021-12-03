<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Actions\Action;
use Cone\Root\Exceptions\ActionResolutionException;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

class Actions extends Collection
{
    /**
     * Filter the actions that are visible for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return static
     */
    public function available(Request $request): static
    {
        return $this->filter(static function (Action $item) use ($request): bool {
                        return $item->visible($request);
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
}
