<?php

namespace Cone\Root\Form\Fields;

class Boolean extends Field
{
    /**
     * The Vue component.
     */
    protected string $component = 'Checkbox';

    /**
     * Create a new file field instance.
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->type('checkbox');
    }
}
