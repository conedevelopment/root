<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Resources\Resource;
use Cone\Root\Widgets\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class Widgets extends Collection
{
    /**
     * Filter the widgets that are visible for the given request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return static
     */
    public function available(Request $request): static
    {
        return $this->filter(static function (Widget $widget) use ($request): bool {
                        return $widget->authorized($request) && $widget->visible($request);
                    })
                    ->values();
    }

    /**
     * Call the resolved callbacks on the widgets.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  string  $key
     * @return void
     */
    public function resolved(Request $request, Resource $resource, string $key): void
    {
        $this->each(static function (Widget $widget) use ($request, $resource, $key): void {
            $widget->resolved($request, $resource, sprintf('%s/%s', $key, $widget->getKey()));
        });
    }
}
