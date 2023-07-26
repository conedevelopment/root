<?php

namespace Cone\Root\Table;

use Cone\Root\Interfaces\Routable;
use Cone\Root\Table\Actions\Actions;
use Cone\Root\Table\Cells\Actions as ActionsCell;
use Cone\Root\Table\Cells\Cell;
use Cone\Root\Table\Cells\Select;
use Cone\Root\Table\Columns\Column;
use Cone\Root\Table\Columns\Columns;
use Cone\Root\Table\Columns\Text;
use Cone\Root\Table\Filters\Filters;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesActions;
use Cone\Root\Traits\ResolvesColumns;
use Cone\Root\Traits\ResolvesFilters;
use Cone\Root\Traits\ResolvesQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Traits\Macroable;

class Table implements Renderable, Routable
{
    use Macroable;
    use Makeable;
    use ResolvesActions;
    use ResolvesColumns;
    use ResolvesFilters;
    use ResolvesQuery;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }

    /**
     * The blade template.
     */
    protected string $template = 'root::table.table';

    /**
     * Create a new table instance.
     */
    public function __construct()
    {
        $this->columns = new Columns($this, $this->columns());
        $this->actions = new Actions($this, $this->actions());
        $this->filters = new Filters($this, $this->filters());
    }

    /**
     * Resolve the filtered query.
     */
    public function resolveFilteredQuery(Request $request): Builder
    {
        return $this->filters->apply($request);
    }

    /**
     * Perform the query and the pagination.
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $url = $this->replaceRoutePlaceholders($request->route());

        return $this->resolveFilteredQuery($request)
            ->latest()
            ->paginate($request->input('per_page'))
            ->setPath($url)
            ->withQueryString()
            ->through(function (Model $model) use ($url): array {
                return $this->columns->map(static function (Column $column) use ($model): Cell {
                    return $column->toCell($model)
                        ->value($column->getValueResolver())
                        ->format($column->getFormatResolver());
                })
                    ->when($this->actions->isNotEmpty(), function (Collection $cells) use ($model): void {
                        $cells->prepend(Select::make(Text::make($this, ''), $model));
                    })
                    ->push(ActionsCell::make(Text::make($this, ''), $model)->value(static function (Model $model) use ($url): string {
                        return sprintf('%s%s', $url, $model->getRouteKey());
                    }))
                    ->all();
            });
    }

    /**
     * Get the view data.
     */
    public function data(Request $request): array
    {
        return [
            'columns' => $this->columns->all(),
            'actions' => $this->actions->all(),
            'filters' => $this->filters->all(),
            'items' => $this->paginate($request),
        ];
    }

    /**
     * Render the table.
     */
    public function render(): View
    {
        return App::make('view')->make(
            $this->template,
            App::call([$this, 'data'])
        );
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return '';
    }

    /**
     * Register the routes.
     */
    public function registerRoutes(Router $router): void
    {
        $this->__registerRoutes($router);

        $this->actions->registerRoutes($router);
    }
}
