<?php

namespace Cone\Root\Columns\Cells;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class RowActions extends Cell
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::columns.cells.row-actions';

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            App::call(function (Request $request): array {
                return [
                    'model' => $this->getModel(),
                    'url' => $this->resolveValue($request),
                ];
            })
        );
    }
}
