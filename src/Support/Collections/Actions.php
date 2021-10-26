<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Actions\Action;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Actions extends Collection
{
    /**
     * Filter the actions that are visible for the given request and action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $action
     * @return static
     */
    public function filterVisibleFor(Request $request, string $action): static
    {
        return $this->filter(static function (Action $item) use ($request, $action): bool {
                        return $item->visible($request, $action);
                    })
                    ->values();
    }
}
