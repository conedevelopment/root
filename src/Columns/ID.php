<?php

namespace Cone\Root\Columns;

class ID extends Column
{
    /**
     * Create a new column instance.
     */
    public function __construct(string $label = 'ID', string $modelAttribute = 'id')
    {
        parent::__construct($label, $modelAttribute);

        $this->sortable();
    }
}
