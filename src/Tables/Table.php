<?php

namespace Cone\Root\Tables;

use Closure;
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
     * The query instance.
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
     * Set the row resolver callback.
     */
    protected ?Closure $rowResolver = null;

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
     * Set the row resovler callback.
     */
    public function row(Closure $callback): static
    {
        $this->rowResolver = $callback;

        return $this;
    }

    /**
     * Get the table data.
     */
    public function toData(Request $request): array
    {
        return [
            'actions' => $this->actions->mapToForm($request, $this->query->getModel())->toArray(),
            'filters' => $this->filters->mapToForm($request)->toArray(),
            'items' => array_merge($this->toRows($request), [
                'query' => $this->filters->mapToQuery($request, $this->query),
            ]),
        ];
    }

    /**
     * Get the table rows.
     */
    public function toRows(Request $request): array
    {
        return $this->filters
                    ->apply($request, $this->query)
                    ->latest()
                    ->paginate($request->input('per_page'))
                    ->withQueryString()
                    ->setPath(Str::start($request->path(), '/'))
                    ->through(fn (Model $model): array => $this->toRow($request, $model)->build($request))
                    ->toArray();
    }

    /**
     * Get a table row.
     */
    public function toRow(Request $request, Model $model): Row
    {
        $row = new Row($model, $this->fields);

        return is_null($this->rowResolver)
            ? $row
            : call_user_func_array($this->rowResolver, [$row, $model, $request]);
    }
}
