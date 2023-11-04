<?php

namespace Cone\Root\Fields;

class ID extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct(string $label = 'ID', string $modelAttribute = 'id')
    {
        parent::__construct($label, $modelAttribute);

        $this->hiddenOn(['crate', 'update']);
    }
}
