<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Widgets\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Widgets extends Collection
{
    /**
     * Filter the widgets that are visible for the given request and action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $action
     * @return static
     */
    public function filterVisible(Request $request, ?string $action = null): static
    {
        return $this->filter(static function (Widget $widget) use ($request, $action): bool {
                        return $widget->visible($request, $action);
                    })
                    ->values();
    }
}
