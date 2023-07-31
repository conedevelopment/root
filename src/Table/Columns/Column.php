<?php

namespace Cone\Root\Table\Columns;

use Closure;
use Cone\Root\Table\Cells\Cell;
use Cone\Root\Table\Table;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Stringable;

abstract class Column implements Stringable
{
    use Makeable;

    /**
     * The label.
     */
    protected string $label;

    /**
     * The key.
     */
    protected string $key;

    /**
     * The Blade template.
     */
    protected string $template = 'root::table.column';

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
     * The table instance.
     */
    protected Table $table;

    /**
     * Create a new column instance.
     */
    public function __construct(Table $table, string $label, string $key = null)
    {
        $this->label = $label;
        $this->key = $key ??= Str::of($label)->lower()->snake()->value();
        $this->table = $table;
    }

    /**
     * Convert the column to a cell.
     */
    abstract public function toCell(Model $model): Cell;

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Set the value resolver.
     */
    public function value(Closure $callback): static
    {
        $this->valueResolver = $callback;

        return $this;
    }

    /**
     * Get the value resolver.
     */
    public function getValueResolver(): Closure
    {
        return $this->valueResolver ?: static function (Model $model, mixed $value): mixed {
            return $value;
        };
    }

    /**
     * Set the format resolver.
     */
    public function format(Closure $callback): static
    {
        $this->formatResolver = $callback;

        return $this;
    }

    /**
     * Get the format resolver.
     */
    public function getFormatResolver(): Closure
    {
        return $this->valueResolver ?: static function (Model $model, mixed $value): mixed {
            return $value;
        };
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
     * The view data.
     */
    public function data(Request $request): array
    {
        return [
            'sortable' => $this->isSortable(),
            'label' => $this->label,
            'key' => $this->key,
        ];
    }

    /**
     * Render the column.
     */
    public function render(): View
    {
        return App::make('view')->make(
            $this->template,
            App::call([$this, 'data'])
        );
    }

    /**
     * Convert the column to a string.
     */
    public function __toString(): string
    {
        return $this->render()->render();
    }
}
