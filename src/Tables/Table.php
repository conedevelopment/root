<?php

namespace Cone\Root\Tables;

use Closure;
use Cone\Root\Support\Collections\Actions;
use Cone\Root\Support\Collections\Filters;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Table implements Arrayable
{
    /**
     * The model instance.
     */
    protected Model $model;

    /**
     * The columns collection.
     */
    protected Columns $columns;

    /**
     * The filters collection.
     */
    protected Filters $filters;

    /**
     * The actions collection.
     */
    protected Actions $actions;

    /**
     * The Vue component.
     */
    protected string $component = 'Table';

    /**
     * The Eloquent query.
     */
    protected ?Builder $query = null;

    /**
     * The query resolver callback.
     */
    protected ?Closure $queryResolver = null;

    /**
     * Create a new table instance.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->actions = new Actions($this->actions());
        $this->columns = new Columns($this->columns());
        $this->filters = new Filters($this->filters());
    }

    /**
     * The default columns.
     */
    public function columns(): array
    {
        return [];
    }

    /**
     * Merge the given columns into the collection.
     */
    public function withColumns(array $columns): static
    {
        $this->columns->push(...$columns);

        return $this;
    }

    /**
     * The default actions.
     */
    public function actions(): array
    {
        return [];
    }

    /**
     * Merge the given actions into the collection.
     */
    public function withActions(array $actions): static
    {
        $this->actions->merge($actions);

        return $this;
    }

    /**
     * The default filters.
     */
    public function filters(): array
    {
        return [];
    }

    /**
     * Merge the given columns into the collection.
     */
    public function withFilters(array $filters): static
    {
        $this->filters->merge($filters);

        return $this;
    }

    /**
     * Set the query resolver callback.
     */
    public function withQuery(Closure $callback): static
    {
        $this->queryResolver = $callback;

        return $this;
    }

    /**
     * Resolve the query.
     */
    public function resolveQuery(): Builder
    {
        if (! is_null($this->query)) {
            return $this->query;
        }

        $this->query = is_null($this->queryResolver)
            ? $this->model->newQuery()
            : call_user_func_array($this->queryResolver, [$this->model->newQuery()]);

        return $this->query;
    }

    /**
     * Map the table rows.
     */
    public function toRows(Request $request): array
    {
        // $filters = $this->resolveFilters($request)->available($request);

        return $this->resolveQuery()
                    ->latest()
                    ->paginate($request->input('per_page'))
                    ->withQueryString()
                    // ->setPath($this->getUri())
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
            'columns' => $this->columns->map->toDisplay($request, $model)->toArray(),
            'abilities' => [],
        ];
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return [
            'component' => $this->component,
        ];
    }

    /**
     * Convert the table to processable data.
     */
    public function toData(Request $request): array
    {
        return array_merge($this->toArray(), [
            'items' => $this->toRows($request),
        ]);
    }
}
