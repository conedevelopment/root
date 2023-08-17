<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Form\Form;
use Cone\Root\Traits\RegistersRoutes;

class Repeater extends Fieldset
{
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }

    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.repeater';

    /**
     * Create a new field instance.
     */
    public function __construct(Form $form, string $label, string $key)
    {
        parent::__construct($form, $label, $key);

        //
    }
}
