<?php

namespace Cone\Root\Traits;

use Cone\Root\Fields\Fields;
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

            $this->resolveFields($request)
                ->searchable()
                ->whenNotEmpty(function (Fields $fields): void {
                    $this->filters->prepend(new Search($fields));
                });

            $this->resolveFields($request)
                ->sortable()
                ->whenNotEmpty(function (Fields $fields): void {
                    $this->filters->register(new Sort($fields));
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
