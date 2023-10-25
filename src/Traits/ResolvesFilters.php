<?php

namespace Cone\Root\Traits;

use Cone\Root\Columns\Columns;
use Cone\Root\Filters\Filter;
use Cone\Root\Filters\Filters;
use Cone\Root\Filters\Search;
use Cone\Root\Filters\Sort;
use Cone\Root\Filters\TrashStatus;
use Illuminate\Database\Eloquent\SoftDeletes;
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
            //
        ];
    }

    /**
     * Resolve the filters collection.
     */
    public function resolveFilters(Request $request): Filters
    {
        if (is_null($this->filters)) {
            $this->filters = new Filters($this->filters($request));

            $this->resolveColumns($request)
                ->searchable()
                ->whenNotEmpty(function (Columns $columns): void {
                    $this->filters->prepend(new Search($columns));
                });

            $this->resolveColumns($request)
                ->sortable()
                ->whenNotEmpty(function (Columns $columns): void {
                    $this->filters->register(new Sort($columns));
                });

            if (in_array(SoftDeletes::class, class_uses_recursive($this->getModel()))) {
                $this->filters->register(new TrashStatus());
            }

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
