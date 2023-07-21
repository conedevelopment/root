<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Form\Form;

class Radio extends Select
{
    /**
     * The blade template.
     */
    protected string $template = 'root::form.fields.radio';

    /**
     * Create a new field instance.
     */
    public function __construct(Form $form, string $label, string $name = null)
    {
        parent::__construct($form, $label, $name);

        $this->type('radio');
    }
}
