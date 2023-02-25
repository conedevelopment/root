<?php

namespace Cone\Root\Fields;

class Checkbox extends Select
{
    /**
     * The Vue component.
     */
    protected string $component = 'Checkbox';

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->type('checkbox');
    }
}
