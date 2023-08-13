<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Form\Form;

class Schema extends Fieldset
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.schema';

    /**
     * Create a new schema instance.
     */
    public function __construct(Form $form)
    {
        //
    }
}
