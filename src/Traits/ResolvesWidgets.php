<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Support\Collections\Widgets;
use Illuminate\Http\Request;

trait ResolvesWidgets
{
    /**
     * The widgets resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $widgetsResolver = null;

    /**
     * The resolved fields.
     *
     * @var \Cone\Root\Support\Collections\Widgets|null
     */
    protected ?Widgets $resolvedWidgets = null;

    /**
     * Define the widgets for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function widgets(Request $request): array
    {
        return [];
    }

    /**
     * Set the widgets resolver.
     *
     * @param  array|\Closure  $widgets
     * @return $this
     */
    public function withWidgets(array|Closure $widgets): static
    {
        if (is_array($widgets)) {
            $widgets = static function (Request $request, Widgets $collection) use ($widgets): Widgets {
                return $collection->merge($widgets);
            };
        }

        $this->widgetsResolver = $widgets;

        return $this;
    }

    /**
     * Resolve the widgets.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Widgets
     */
    public function resolveWidgets(Request $request): Widgets
    {
        if (is_null($this->resolvedWidgets)) {
            $widgets = Widgets::make($this->widgets($request));

            if (! is_null($this->widgetsResolver)) {
                $widgets = call_user_func_array($this->widgetsResolver, [$request, $widgets]);
            }

            $this->resolvedWidgets = $widgets->each->mergeAuthorizationResolver(function (...$parameters): bool {
                return $this->authorized(...$parameters);
            });
        }

        return $this->resolvedWidgets;
    }
}
