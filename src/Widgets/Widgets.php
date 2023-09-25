<?php

namespace Cone\Root\Widgets;

use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Traits\ForwardsCalls;

class Widgets
{
    use ForwardsCalls;

    /**
     * The widgets collection.
     */
    protected Collection $widgets;

    /**
     * Create a new widgets instance.
     */
    public function __construct(array $widgets = [])
    {
        $this->widgets = new Collection($widgets);
    }

    /**
     * Register the given widgets.
     */
    public function register(array|Widget $widgets): static
    {
        foreach (Arr::wrap($widgets) as $widget) {
            $this->widgets->push($widget);
        }

        return $this;
    }

    /**
     * Make a new widget instance.
     */
    public function widget(string $widget, ...$params): Widget
    {
        $instance = new $widget(...$params);

        $this->register($instance);

        return $instance;
    }

    /**
     * Handle the dynamic method call.
     */
    public function __call($method, $parameters): mixed
    {
        return $this->forwardCallTo($this->widgets, $method, $parameters);
    }
}
