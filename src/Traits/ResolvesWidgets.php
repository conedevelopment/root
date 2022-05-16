<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Support\Collections\Widgets;
use Cone\Root\Widgets\Widget;

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
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function widgets(RootRequest $request): array
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
            $widgets = static function (RootRequest $request, Widgets $collection) use ($widgets): Widgets {
                return $collection->merge($widgets);
            };
        }

        $this->widgetsResolver = $widgets;

        return $this;
    }

    /**
     * Resolve the widgets.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Cone\Root\Support\Collections\Widgets
     */
    public function resolveWidgets(RootRequest $request): Widgets
    {
        if (is_null($this->resolvedWidgets)) {
            $widgets = Widgets::make($this->widgets($request));

            if (! is_null($this->widgetsResolver)) {
                $widgets = call_user_func_array($this->widgetsResolver, [$request, $widgets]);
            }

            $this->resolvedWidgets = $widgets->each(function (Widget $widget) use ($request): void {
                $this->resolveWidget($request, $widget);
            });
        }

        return $this->resolvedWidgets;
    }

    /**
     * Handle the resolving event on the widget instance.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Cone\Root\Widgets\Widget  $widget
     * @return void
     */
    protected function resolveWidget(RootRequest $request, Widget $widget): void
    {
        //
    }
}
