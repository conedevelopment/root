<?php

namespace Cone\Root\Columns;

use Closure;
use Cone\Root\Columns\Cells\Cell;
use Cone\Root\Support\Element;
use Cone\Root\Traits\Makeable;
use Illuminate\Database\Eloquent\Model;
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
        return array_merge(parent::toArray(), [
            'attribute' => $this->modelAttribute,
            'label' => $this->label,
            'sortable' => $this->isSortable(),
        ]);
    }
}
