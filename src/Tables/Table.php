<?php

namespace Cone\Root\Tables;

use Closure;
use Cone\Root\Actions\Action;
use Cone\Root\Tables\Columns\Column;
use Cone\Root\Traits\ResolvesActions;
use Cone\Root\Traits\ResolvesFilters;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class Table implements Arrayable
{
    use ResolvesActions;
    use ResolvesFilters;

    /**
     * The model instance.
     */
    protected Model $model;

    /**
     * The columns resolver callback.
     */
    protected ?Closure $columnsResolver = null;

    /**
     * The resolved columns.
     */
    protected ?Columns $columns = null;

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
    }

    /**
     * The default columns.
     */
    public function columns(Request $request): array
    {
        return [];
    }

    /**
     * Merge the given columns into the collection.
     */
    public function withColumns(Closure|array $columns): static
    {
        $this->columnsResolver = is_array($columns) ? fn (): array => $columns : $columns;

        return $this;
    }

    /**
     * Resolve the actions.
     */
    public function resolveColumns(Request $request): Columns
    {
        if (is_null($this->columns)) {
            $this->columns = Columns::make()->register($this->columns($request));

            if (! is_null($this->columnsResolver)) {
                $this->columns->register(call_user_func_array($this->columnsResolver, [$request]));
            }

            $this->columns->each(function (Column $column) use ($request): void {
                $this->resolveColumn($request, $column);
            });
        }

        return $this->columns;
    }

    /**
     * Handle the resolving event on the action instance.
     */
    protected function resolveColumn(Request $request, Column $column): void
    {
        //
    }

    /**
     * Handle the resolving event on the action instance.
     */
    protected function resolveAction(Request $request, Action $action): void
    {
        $action->withQuery(function (Request $request): Builder {
            return $this->resolveFilters($request)
                        ->available($request)
                        ->apply($request, $this->resolveQuery());
        });
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
        return $this->resolveFilters($request)
                    ->apply($request, $this->resolveQuery())
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
            'columns' => $this->resolveColumns($request)->map->toDisplay($request, $model)->toArray(),
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
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return [
            'component' => $this->component,
        ];
    }

    /**
     * Build the table.
     */
    public function build(Request $request): array
    {
        return array_merge($this->toArray(), [
            'items' => $this->toRows($request),
            'filters' => $this->resolveFilters($request)->mapToForm($request)->toArray(),
            'actions' => $this->resolveActions($request)->mapToForm($request, $this->model)->toArray(),
            'filter_values' => $this->resolveFilters($request)->mapToQuery($request, $this->resolveQuery()),
        ]);
    }
}
