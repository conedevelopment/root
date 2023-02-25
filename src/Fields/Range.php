<?php

namespace Cone\Root\Fields;

class Range extends Number
{
    /**
     * The Vue component.
     */
    protected string $component = 'Range';

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->type('range')->step(1)->min(0)->max(100);
    }
}
