<?php

namespace Cone\Root\Fields;

class Hidden extends Field
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.hidden';

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, string $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->type('hidden');
    }
}
