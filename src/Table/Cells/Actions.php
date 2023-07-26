<?php

namespace Cone\Root\Table\Cells;

use Illuminate\Http\Request;

class Actions extends Cell
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::table.cells.row-actions';

    /**
     * The view data.
     */
    public function data(Request $request): array
    {
        return [
            'model' => $this->resolveModel(),
            'url' => $this->resolveValue(),
        ];
    }
}
