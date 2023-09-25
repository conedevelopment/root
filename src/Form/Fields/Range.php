<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Form\Form;

class Range extends Number
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.range';

    /**
     * Create a new field instance.
     */
    public function __construct(Form $form, string $label, string $modelAttribute = null)
    {
        parent::__construct($form, $label, $modelAttribute);

        $this->type('range')->step(1)->min(0)->max(100);
    }
}
