<?php

namespace Cone\Root\Table;

use Illuminate\Http\Request;

class SelectCell extends Cell
{
    /**
     * The blade template.
     */
    protected string $template = 'root::table.select-cell';

    /**
     * The view data.
     */
    public function data(Request $request): array
    {
        return [
            'model' => $this->model,
        ];
    }
}
