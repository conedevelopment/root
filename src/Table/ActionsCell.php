<?php

namespace Cone\Root\Table;

use Illuminate\Http\Request;

class ActionsCell extends Cell
{
    /**
     * The blade template.
     */
    protected string $template = 'root::table.actions-cell';

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
