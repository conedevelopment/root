<?php

namespace Cone\Root\Table\Cells;

use Illuminate\Http\Request;

class Select extends Cell
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::table.cells.row-select';

    /**
     * The view data.
     */
    public function data(Request $request): array
    {
        return [
            'model' => $this->resolveModel(),
        ];
    }
}
