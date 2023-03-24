<?php

namespace Cone\Root\Support\Collections;

use Cone\Root\Widgets\Widget;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Widgets extends Collection
{
    /**
     * Register the given widgets.
     */
    public function register(array|Widget $widgets): static
    {
        foreach (Arr::wrap($widgets) as $widget) {
            $this->push($widget);
        }

        return $this;
    }
}
