<?php

namespace Cone\Root\Tables;

use Cone\Root\Support\Collections\Actions;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Support\Collections\Filters;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Table
{
    /**
     * The Eloquent query intance.
     */
    protected Builder $query;

    /**
     * The fields collection.
     */
    protected Fields $fields;

    /**
     * The actions collection.
     */
    protected Actions $actions;

    /**
     * The filters collection.
     */
    protected Filters $filters;

    /**
     * Create a new table instance.
     */
    public function __construct(Builder $query, Fields $fields, Actions $actions, Filters $filters)
    {
        $this->query = $query;
        $this->fields = $fields;
        $this->actions = $actions;
        $this->filters = $filters;
    }

    /**
     * Map the table rows.
     */
    public function toRows(Request $request): array
    {
        return $this->filters
                    ->apply($request, $this->query)
                    ->latest()
                    ->paginate($request->input('per_page'))
                    ->withQueryString()
                    ->setPath(Str::start($request->path(), '/'))
                    ->through(function (Model $model) use ($request): array {
                        return $this->toRow($request, $model);
                    })
                    ->toArray();
    }

    /**
     * Map a table row.
     */
    public function toRow(Request $request, Model $model): array
    {
        return [
            'id' => $model->getKey(),
            'columns' => $this->fields->mapToDisplay($request, $model)->toArray(),
            'trashed' => false,
            'url' => '',
            'abilities' => [
                'create' => true,
                'update' => true,
                'view' => true,
                'delete' => true,
            ],
        ];
    }

    /**
     * Build the table.
     */
    public function build(Request $request): array
    {
        return [
            'items' => $this->toRows($request),
            'filters' => $this->filters->mapToForm($request)->toArray(),
            'actions' => $this->actions->mapToForm($request, $this->query->getModel())->toArray(),
            'filter_values' => $this->filters->mapToQuery($request, $this->query),
        ];
    }
}
