<?php

namespace Cone\Root\Widgets;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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

    /**
     * Filter the fields that are available for the current request and model.
     */
    public function authorized(Request $request, Model $model = null): static
    {
        return $this->filter->authorized($request, $model)->values();
    }

    /**
     * Filter the fields that are visible in the given context.
     */
    public function visible(string|array $context): static
    {
        return $this->filter->visible($context)->values();
    }
}
