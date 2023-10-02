<?php

namespace Cone\Root\Traits;

use Cone\Root\Filters\Filter;
use Cone\Root\Filters\Filters;
use Cone\Root\Filters\Search;
use Cone\Root\Filters\TrashStatus;
use Illuminate\Http\Request;

trait ResolvesFilters
{
    /**
     * The filters collection.
     */
    protected ?Filters $filters = null;

    /**
     * Define the filters for the object.
     */
    public function filters(Request $request): array
    {
        return [
            new Search(),
            // new Sort(),
            new TrashStatus(),
        ];
    }

    /**
     * Resolve the filters collection.
     */
    public function resolveFilters(Request $request): Filters
    {
        if (is_null($this->filters)) {
            $this->filters = new Filters($this->filters($request));

            $this->filters->each(function (Filter $filter) use ($request): void {
                $this->resolveFilter($request, $filter);
            });
        }

        return $this->filters;
    }

    /**
     * Handle the callback for the filter resolution.
     */
    protected function resolveFilter(Request $request, Filter $filter): void
    {
        //
    }
}
