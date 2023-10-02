<?php

namespace Cone\Root\Fields;

class Color extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct(string $label, string $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->type('color');
    }
}
