<?php

namespace Cone\Root\Table;

use Cone\Root\Support\Collections\Actions;
use Cone\Root\Support\Collections\Filters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class Table
{
    /**
     * The query instance.
     */
    protected Builder $query;

    /**
     * The columns.
     */
    protected Columns $columns;

    /**
     * The filters.
     */
    protected Filters $filters;

    /**
     * The actions.
     */
    protected Actions $actions;

    /**
     * The base url.
     */
    protected ?string $url = null;

    /**
     * The table key.
     */
    protected ?string $key = null;

    /**
     * Create a new table instance.
     */
    public function __construct(Builder $query, Columns $columns, Filters $filters, Actions $actions)
    {
        $this->query = $query;
        $this->columns = $columns;
        $this->filters = $filters;
        $this->actions = $actions;
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
            ->withQueryString();
    }

    /**
     * Build the table data.
     */
    public function build(Request $request): array
    {
        return [
            'items' => $this->paginate($request),
            'columns' => $this->columns,
            'actions' => $this->actions,
            'filters' => $this->filters,
        ];
    }
}
