<?php

namespace Cone\Root\Traits;

use Cone\Root\Columns\Column;
use Cone\Root\Columns\Columns;
use Cone\Root\Columns\RowActions;
use Cone\Root\Columns\RowSelect;
use Illuminate\Http\Request;

trait ResolvesColumns
{
    /**
     * The columns collection.
     */
    protected ?Columns $columns = null;

    /**
     * Define the columns for the object.
     */
    public function columns(Request $request): array
    {
        return [
            //
        ];
    }

    /**
     * Resolve the columns collection.
     */
    public function resolveColumns(Request $request): Columns
    {
        if (is_null($this->columns)) {
            $this->columns = new Columns($this->columns($request));

            if ($this->resolveActions($request)->isNotEmpty()) {
                $this->columns->prepend(new RowSelect(__('Select'), 'id'));
            }

            $this->columns->push(new RowActions(__('Actions'), 'id'));

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
}
