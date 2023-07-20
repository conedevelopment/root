<?php

namespace Cone\Root\Form\Fields;

class Color extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->type('color');
    }
}
