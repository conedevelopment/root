<?php

namespace Cone\Root\Widgets;

use Illuminate\Http\Request;

abstract class Trend extends Metric
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::widgets.trend';

    /**
     * Calculate the metric data.
     */
    public function calculate(Request $request): array
    {
        return [];
    }
}
