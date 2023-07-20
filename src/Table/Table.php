<?php

namespace Cone\Root\Table;

use Cone\Root\Actions\Action;
use Cone\Root\Interfaces\Renderable;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Support\Collections\Actions;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesActions;
use Cone\Root\Traits\ResolvesColumns;
use Cone\Root\Traits\ResolvesFilters;
use Cone\Root\Traits\ResolvesQuery;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;

class Table implements Renderable, Routable
{
    use Makeable;
    use ResolvesActions;
    use ResolvesColumns;
    use ResolvesFilters;
    use ResolvesQuery;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }

    /**
     * The blade tempalte.
     */
    protected string $template = 'root::table.table';

    /**
     * Handle the resolving event on the action instance.
     */
    protected function resolveAction(Request $request, Action $action): void
    {
        $action->query(function (Request $request): Builder {
            return $this->resolveFilters($request)
                ->authorized($request)
                ->apply($request, $this->resolveQuery());
        });
    }

    /**
     * Perform the query and the pagination.
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        $url = $this->replaceRoutePlaceholders($request->route());

        return $this->resolveFilters($request)
            ->apply($request, $this->resolveQuery())
            ->latest()
            ->paginate($request->input('per_page'))
            ->setPath($url)
            ->withQueryString()
            ->through(function (Model $model) use ($request, $url): array {
                return $this->resolveColumns($request)
                    ->map(static function (Column $column) use ($model): Cell {
                        return $column->toCell($model);
                    })
                    ->when($this->resolveActions($request)->isNotEmpty(), function (Actions $actions) use ($model): void {
                        $actions->prepend(new SelectCell($model, TextColumn::make($this, '')));
                    })
                    ->push(new ActionsCell($model, TextColumn::make($this, '')->value(fn (Model $model): string => sprintf('%s%s', $url, $model->getRouteKey()))))
                    ->all();
            });
    }

    /**
     * Get the blade template.
     */
    public function template(): string
    {
        return $this->template;
    }

    /**
     * Get the view data.
     */
    public function data(Request $request): array
    {
        return [
            'columns' => $this->resolveColumns($request),
            'actions' => $this->resolveActions($request),
            'filters' => $this->resolveFilters($request),
            'items' => $this->paginate($request),
        ];
    }

    /**
     * Render the table.
     */
    public function render(): View
    {
        return App::make('view')->make(
            $this->template(),
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

        $this->resolveActions(App::make('request'))->registerRoutes($router);
    }
}
