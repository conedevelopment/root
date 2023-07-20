<?php

namespace Cone\Root\Form\Fields;

class Hidden extends Field
{
    /**
     * The Vue component.
     */
    protected string $component = 'Hidden';

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->type('hidden');
    }
}
