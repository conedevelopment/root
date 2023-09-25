<?php

namespace Cone\Root\Table\Cells;

class RowSelect extends Cell
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::table.cells.row-select';

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'model' => $this->getModel(),
        ]);
    }
}
