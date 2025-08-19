<?php

declare(strict_types=1);

namespace Cone\Root\Traits;

use Cone\Root\Widgets\Widget;
use Cone\Root\Widgets\Widgets;
use Illuminate\Http\Request;

trait ResolvesWidgets
{
    /**
     * The widgets collection.
     */
    protected ?Widgets $widgets = null;

    /**
     * Define the widgets.
     */
    public function widgets(Request $request): array
    {
        return [];
    }

    /**
     * Resolve the widgets collection.
     */
    public function resolveWidgets(Request $request): Widgets
    {
        if (is_null($this->widgets)) {
            $this->widgets = new Widgets($this->widgets($request));

            $this->widgets->each(function (Widget $widget) use ($request): void {
                $this->resolveWidget($request, $widget);
            });
        }

        return $this->widgets;
    }

    /**
     * Handle the callback for the widget resolution.
     */
    protected function resolveWidget(Request $request, Widget $widget): void
    {
        //
    }
}
