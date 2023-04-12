<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Support\Collections\Widgets;
use Cone\Root\Widgets\Widget;
use Illuminate\Http\Request;

trait ResolvesWidgets
{
    /**
     * The widgets resolver callback.
     */
    protected ?Closure $widgetsResolver = null;

    /**
     * The resolved fields.
     */
    protected ?Widgets $widgets = null;

    /**
     * Define the widgets for the resource.
     */
    public function widgets(Request $request): array
    {
        return [];
    }

    /**
     * Set the widgets resolver.
     */
    public function withWidgets(array|Closure $widgets): static
    {
        $this->widgetsResolver = is_array($widgets) ? fn (): array => $widgets : $widgets;

        return $this;
    }

    /**
     * Resolve the widgets.
     */
    public function resolveWidgets(Request $request): Widgets
    {
        if (is_null($this->widgets)) {
            $this->widgets = Widgets::make()->register($this->widgets($request));

            if (! is_null($this->widgetsResolver)) {
                $this->widgets->register(call_user_func_array($this->widgetsResolver, [$request]));
            }

            $this->widgets->each(function (Widget $widget) use ($request): void {
                $this->resolveWidget($request, $widget);
            });
        }

        return $this->widgets;
    }

    /**
     * Handle the resolving event on the widget instance.
     */
    protected function resolveWidget(Request $request, Widget $widget): void
    {
        //
    }
}
