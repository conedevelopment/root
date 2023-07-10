<?php

namespace Cone\Root\Table;

use Cone\Root\Interfaces\Renderable;
use Cone\Root\Traits\ResolvesActions;
use Cone\Root\Traits\ResolvesColumns;
use Cone\Root\Traits\ResolvesFilters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;

class Table implements Renderable
{
    use ResolvesColumns;
    use ResolvesActions;
    use ResolvesFilters;

    /**
     * The query instance.
     */
    protected Builder $query;

    /**
     * The base url.
     */
    protected ?string $url = null;

    /**
     * The table key.
     */
    protected ?string $key = null;

    /**
     * The blade tempalte.
     */
    protected string $template = 'root::table.table';

    /**
     * Create a new table instance.
     */
    public function __construct(Builder $query, string $url = null)
    {
        $this->query = $query;
        $this->url($url);
    }

    /**
     * Set the table URL.
     */
    public function url(string $url): static
    {
        $this->url = URL::to($url);

        return $this;
    }

    /**
     * Set the table key.
     */
    public function key(string $key): static
    {
        $this->key = $key;

        return $this;
    }

    /**
     * Perform the query and the pagination.
     */
    public function paginate(Request $request): LengthAwarePaginator
    {
        return $this->filters
            ->apply($request, $this->query)
            ->latest()
            ->paginate($request->input('per_page'))
            ->setPath($this->url ?: $request->path())
            ->withQueryString()
            ->through(function (Model $model) use ($request): array {
                return $this->resolveColumns($request)
                    ->map(static function (Column $column) use ($model): Cell {
                        return $column->toCell($model);
                    })
                    ->prepend(new SelectCell($model, new Column('')))
                    ->push(new ActionsCell($model, new Column('')))
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
}
