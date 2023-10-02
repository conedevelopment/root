<?php

namespace Cone\Root\Fields;

use Cone\Root\Interfaces\Form;

class Range extends Number
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.range';

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, string $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->type('range')->step(1)->min(0)->max(100);
    }
}
