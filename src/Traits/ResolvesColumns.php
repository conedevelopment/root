<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Table\Columns\Column;
use Cone\Root\Table\Columns\Columns;
use Cone\Root\Table\Columns\Text;
use Illuminate\Http\Request;

trait ResolvesColumns
{
    /**
     * The columns resolver callback.
     */
    protected ?Closure $columnsResolver = null;

    /**
     * The resolved columns.
     */
    protected ?Columns $columns = null;

    /**
     * Define the columns for the object.
     */
    public function columns(Request $request): array
    {
        return [];
    }

    /**
     * Set the columns resolver.
     */
    public function withColumns(array|Closure $columns): static
    {
        $this->columnsResolver = is_array($columns) ? fn (): array => $columns : $columns;

        return $this;
    }

    /**
     * Resolve the columns.
     */
    public function resolveColumns(Request $request): Columns
    {
        if (is_null($this->columns)) {
            $this->columns = Columns::make()->register($this->columns($request));

            if (! is_null($this->columnsResolver)) {
                $this->columns->register(call_user_func_array($this->columnsResolver, [$this, $request]));
            }

            $this->columns->each(function (Column $column) use ($request): void {
                $this->resolveColumn($request, $column);
            });
        }

        return $this->columns;
    }

    /**
     * Handle the resolving event on the column instance.
     */
    protected function resolveColumn(Request $request, Column $column): void
    {
        //
    }

    /**
     * Make a new table text column.
     */
    public function textColumn(string $label, string $name = null): Text
    {
        return new Text($this, $label, $name);
    }
}
