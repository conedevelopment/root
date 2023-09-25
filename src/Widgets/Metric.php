<?php

namespace Cone\Root\Widgets;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

abstract class Metric extends Widget
{
    /**
     * Calculate the metric data.
     */
    abstract public function calculate(Request $request): array;

    /**
     * Get the data.
     */
    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            App::call(function (Request $request): array {
                return [
                    'data' => $this->calculate($request),
                ];
            }),
        );
    }
}
