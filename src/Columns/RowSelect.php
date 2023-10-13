<?php

namespace Cone\Root\Columns;

use Illuminate\Http\Request;

class RowSelect extends Column
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::columns.cells.row-select';

    /**
     * {@inheritdoc}
     */
    public function toHead(Request $request): array
    {
        return array_merge(parent::toHead($request), [
            'template' => 'root::columns.select-all',
        ]);
    }
}
