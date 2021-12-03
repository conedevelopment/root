<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Widgets\Widget;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;

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
                        return $widget->visible($request);
                    })
                    ->values();
    }
}
