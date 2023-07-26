<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Widgets\Widgets;

trait ResolvesWidgets
{
    /**
     * The resolved widgets.
     */
    public readonly Widgets $widgets;

    /**
     * Define the widgets.
     */
    public function widgets(): array
    {
        return [];
    }

    /**
     * Apply the callback on the widgets.
     */
    public function withWidgets(Closure $callback): static
    {
        call_user_func_array($callback, [$this->widgets]);

        return $this;
    }
}
