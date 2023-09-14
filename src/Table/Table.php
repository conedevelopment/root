<?php

namespace Cone\Root\Table;

use Closure;
use Cone\Root\Form\Fields\Fields;
use Cone\Root\Interfaces\AsForm;
use Cone\Root\Support\Element;
use Cone\Root\Table\Actions\Action;
use Cone\Root\Table\Actions\Actions;
use Cone\Root\Table\Columns\Column;
use Cone\Root\Table\Columns\Columns;
use Cone\Root\Table\Columns\RowActions;
use Cone\Root\Table\Columns\RowSelect;
use Cone\Root\Table\Filters\Filter;
use Cone\Root\Table\Filters\FilterForm;
use Cone\Root\Table\Filters\Filters;
use Cone\Root\Table\Filters\Search;
use Cone\Root\Table\Filters\TrashStatus;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Macroable;

class Table extends Element implements AsForm
{
    use Macroable;
    use Makeable;

    /**
     * The Blade template.
     */
    protected string $template = 'root::table.table';

    /**
     * The columns collection.
     */
    protected ?Columns $columns = null;

    /**
     * The columns resolver callback.
     */
    protected ?Closure $columnsResolver = null;

    /**
     * The actions collection.
     */
    protected ?Actions $actions = null;

    /**
     * The actions resolver callback.
     */
    protected ?Closure $actionsResolver = null;

    /**
     * The filters collection.
     */
    protected ?Filters $filters = null;

    /**
     * The filters resolver callback.
     */
    protected ?Closure $filtersResolver = null;

    /**
     * The Eloquent query.
     */
    protected Builder $query;

    /**
     * The row URL resolver.
     */
    protected ?Closure $rowUrlResovler = null;

    /**
     * Create a new table instance.
     */
    public function __construct(Builder $query)
    {
        $this->query = $query;

        $this->id(
            Str::of(get_class($query->getModel()))->classBasename()->lower()->plural()->value()
        );
    }

    /**
     * Get the table title.
     */
    public function getTitle(): string
    {
        return __(Str::of(get_class($this->query->getModel()))->classBasename()->headline()->plural()->value());
    }

    /**
     * Get the table query.
     */
    public function getQuery(): Builder
    {
        return $this->query;
    }

    /**
     * Resolve the filtered query.
     */
    public function resolveFilteredQuery(Request $request): Builder
    {
        return $this->resolveFilters($request)->apply($request, $this->getQuery());
    }

    /**
     * Get the per page.
     */
    public function getPerPage(Request $request): ?int
    {
        return $request->input($this->getAttribute('id').':per_page');
    }

    /**
     * Get the per page options.
     */
    public function getPerPageOptions(): array
    {
        return Collection::make([$this->query->getModel()->getPerPage()])
            ->merge([15, 25, 50, 100])
            ->filter()
            ->unique()
            ->values()
            ->toArray();
    }

    /**
     * Get the page name.
     */
    public function getPageName(): string
    {
        return sprintf('%s:page', $this->getAttribute('id'));
    }

    /**
     * Define the columns for the object.
     */
    protected function columns(Request $request, Columns $columns): void
    {
        //
    }

    /**
     * Apply the given callback on the columns.
     */
    public function withColumns(Closure $callback): static
    {
        $this->columnsResolver = $callback;

        return $this;
    }

    /**
     * Resolve the columns collection.
     */
    public function resolveColumns(Request $request): Columns
    {
        if (is_null($this->columns)) {
            $this->columns = new Columns($this);

            if ($this->resolveActions($request)->isNotEmpty()) {
                $this->columns->push(new RowSelect($this, __('Select'), 'id'));
            }

            $this->columns($request, $this->columns);

            if (! is_null($this->columnsResolver)) {
                call_user_func_array($this->columnsResolver, [$request, $this->columns]);
            }

            $this->columns->push(new RowActions($this, __('Actions'), 'id'));

            $this->columns->each(function (Column $column) use ($request): void {
                $this->resolveColumn($request, $column);
            });
        }

        return $this->columns;
    }

    /**
     * Handle the callback for the column resolution.
     */
    protected function resolveColumn(Request $request, Column $column): void
    {
        //
    }

    /**
     * Define the actions for the object.
     */
    protected function actions(Request $request, Actions $actions): void
    {
        //
    }

    /**
     * Set the actions resolver callback.
     */
    public function withActions(Closure $callback): static
    {
        $this->actionsResolver = $callback;

        return $this;
    }

    /**
     * Resolve the actions collection.
     */
    public function resolveActions(Request $request): Actions
    {
        if (is_null($this->actions)) {
            $this->actions = new Actions($this);

            $this->actions($request, $this->actions);

            if (! is_null($this->actionsResolver)) {
                call_user_func_array($this->actionsResolver, [$request, $this->actions]);
            }

            $this->actions->each(function (Action $action) use ($request): void {
                $this->resolveAction($request, $action);
            });
        }

        return $this->actions;
    }

    /**
     * Handle the callback for the action resolution.
     */
    protected function resolveAction(Request $request, Action $action): void
    {
        //
    }

    /**
     * Define the filters for the object.
     */
    protected function filters(Request $request, Filters $filters): void
    {
        $filters->filter(Search::class);
        // $this->filters->filter(Sort::class);
        $filters->filter(TrashStatus::class);
    }

    /**
     * Apply the given callback on the filters.
     */
    public function withFilters(Closure $callback): static
    {
        $this->filtersResolver = $callback;

        return $this;
    }

    /**
     * Resolve the filters collection.
     */
    public function resolveFilters(Request $request): Filters
    {
        if (is_null($this->filters)) {
            $this->filters = new Filters($this);

            $this->filters($request, $this->filters);

            if (! is_null($this->filtersResolver)) {
                call_user_func_array($this->filtersResolver, [$request, $this->filters]);
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

    /**
     * Set the row URL resolver callback.
     */
    public function rowUrl(Closure $callback): static
    {
        $this->rowUrlResovler = $callback;

        return $this;
    }

    /**
     * Resolve the row URL.
     */
    public function resolveRowUrl(Request $request, Model $model): string
    {
        if (is_null($this->rowUrlResovler)) {
            return sprintf('%s/%s', $request->url(), $model->getKey());
        }

        return call_user_func_array($this->rowUrlResovler, [$request, $model]);
    }

    /**
     * Perform the query and the pagination.
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        return $this->resolveFilteredQuery($request)
            ->latest()
            ->paginate($this->getPerPage($request), ['*'], $this->getPageName())
            ->setPath($request->path())
            ->withQueryString()
            ->through(function (Model $model) use ($request): array {
                return [
                    'id' => $model->getKey(),
                    'cells' => $this->resolveColumns($request)->mapToCells($model),
                ];
            });
    }

    /**
     * Make a new form instance.
     */
    public function toForm(Request $request, Model $model): FilterForm
    {
        $data = $this->resolveFilters($request)->mapToData($request);

        $model->forceFill($data);

        return (new FilterForm($model, $request->fullUrl()))
            ->id(sprintf('%s-filters', $this->getAttribute('id')))
            ->withFields(function (Request $request, Fields $fields): void {
                $this->resolveFilters($request)
                    ->renderable()
                    ->each(function (Filter $filter) use ($fields): void {
                        $fields->push($filter->toField($fields->form));
                    });
            })
            ->setAttribute('data-active', $this->resolveFilters($request)->active($request)->count());
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            App::call(function (Request $request): array {
                return [
                    'actions' => $this->resolveActions($request)->all(),
                    'columns' => $this->resolveColumns($request)->all(),
                    'filters' => $this->resolveFilters($request)->all(),
                    'form' => $this->toForm($request, $this->query->getModel()),
                    'items' => $this->paginate($request),
                    'perPageOptions' => $this->getPerPageOptions(),
                    'title' => $this->getTitle(),
                ];
            })
        );
    }
}
