<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Form;
use Cone\Root\Traits\RegistersRoutes;

class Flexible extends Field
{
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }

    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.flexible';

    /**
     * The registered schemas.
     */
    protected array $schemas = [];

    /**
     * Create a new field instance.
     */
    public function __construct(Form $form, string $label, string $key)
    {
        parent::__construct($form, $label, $key);

        //
    }

    /**
     * Add a schema to the flexible field.
     */
    public function schema(string|Closure $schema): static
    {
        $this->schemas = call_user_func_array($schema, [$this, $this->form]);

        return $this;
    }
}
