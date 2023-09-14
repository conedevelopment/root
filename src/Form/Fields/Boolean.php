<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Form\Form;
use Illuminate\Http\Request;

class Boolean extends Field
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.boolean';

    /**
     * Create a new file field instance.
     */
    public function __construct(Form $form, string $label, string $modelAttribute = null)
    {
        parent::__construct($form, $label, $modelAttribute);

        $this->type('checkbox');
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForHydrate(Request $request): mixed
    {
        return $request->boolean([$this->getRequestKey()]);
    }

    /**
     * Set the "checked" HTML attribute.
     */
    public function checked(bool $value = true): static
    {
        $this->setAttribute('checked', $value);

        return $this;
    }

    /**
     * Create a new method.
     */
    public function resolveValue(Request $request): mixed
    {
        $value = parent::resolveValue($request);

        $this->checked(filter_var($value, FILTER_VALIDATE_BOOL));

        return $value;
    }
}
