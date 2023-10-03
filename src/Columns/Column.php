<?php

namespace Cone\Root\Columns;

use Closure;
use Cone\Root\Columns\Cells\Cell;
use Cone\Root\Interfaces\Table;
use Cone\Root\Support\Element;
use Cone\Root\Traits\Makeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Column extends Element
{
    use Makeable;

    /**
     * The label.
     */
    protected string $label;

    /**
     * The Blade template.
     */
    protected string $template = 'root::columns.column';

    /**
     * Indicates if the field is sortable.
     */
    protected bool|Closure $sortable = false;

    /**
     * Indicates if the field is searchable.
     */
    protected bool|Closure $searchable = false;

    /**
     * The format resolver callback.
     */
    protected ?Closure $formatResolver = null;

    /**
     * The value resolver callback.
     */
    protected ?Closure $valueResolver = null;

    /**
     * The associated model attribute.
     */
    protected string $modelAttribute;

    /**
     * The table instance.
     */
    protected ?Table $table = null;

    /**
     * Create a new column instance.
     */
    public function __construct(string $label, string $modelAttribute = null)
    {
        $this->label = $label;
        $this->modelAttribute = $modelAttribute ??= Str::of($label)->lower()->snake()->value();
    }

    /**
     * Get the model attribute.
     */
    public function getModelAttribute(): string
    {
        return $this->modelAttribute;
    }

    /**
     * Set the table instance.
     */
    public function setTable(Table $table): static
    {
        $this->table = $table;

        return $this;
    }

    /**
     * Set the sortable attribute.
     */
    public function sortable(bool|Closure $value = true): static
    {
        $this->sortable = $value;

        return $this;
    }

    /**
     * Determine if the field is sortable.
     */
    public function isSortable(): bool
    {
        if ($this->sortable instanceof Closure) {
            return call_user_func($this->sortable);
        }

        return $this->sortable;
    }

    /**
     * Get the sort URL.
     */
    public function getSortUrl(Request $request): ?string
    {
        if (! $this->isSortable()) {
            return null;
        }

        return match ($request->query('sort', 'asc')) {
            'asc' => $request->fullUrlWithQuery(['sort' => 'desc', 'sort_by' => $this->getModelAttribute()]),
            default => $request->fullUrlWithQuery(['sort' => 'asc', 'sort_by' => $this->getModelAttribute()]),
        };
    }

    /**
     * Set the searachable attribute.
     */
    public function searchable(bool|Closure $value = true): static
    {
        $this->searchable = $value;

        return $this;
    }

    /**
     * Determine if the field is searchable.
     */
    public function isSearchable(): bool
    {
        if ($this->searchable instanceof Closure) {
            return call_user_func($this->searchable);
        }

        return $this->searchable;
    }

    /**
     * Set the value resolver callback.
     */
    public function value(Closure $callback): static
    {
        $this->valueResolver = $callback;

        return $this;
    }

    /**
     * Set the format resolver callback.
     */
    public function format(Closure $callback): static
    {
        $this->formatResolver = $callback;

        return $this;
    }

    /**
     * Make a new cell instance.
     */
    public function newCell(Model $model): Cell
    {
        return new Cell($this, $model);
    }

    /**
     * Convert the column to a cell.
     */
    public function toCell(Model $model): Cell
    {
        return $this->newCell($model)
            ->when(! is_null($this->valueResolver), function (Cell $cell) {
                $cell->value($this->valueResolver);
            })
            ->when(! is_null($this->formatResolver), function (Cell $cell) {
                $cell->format($this->formatResolver);
            });
    }

    /**
     * The view data.
     */
    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            App::call(function (Request $request): array {
                return [
                    'attribute' => $this->modelAttribute,
                    'label' => $this->label,
                    'sortable' => $this->isSortable(),
                    'sortUrl' => $this->getSortUrl($request),
                ];
            })
        );
    }
}
