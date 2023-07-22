<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Form\Form;
use Illuminate\Http\Request;

class Boolean extends Field
{
    /**
     * The blade template.
     */
    protected string $template = 'root::form.fields.boolean';

    /**
     * Create a new file field instance.
     */
    public function __construct(Form $form, string $label, string $name = null)
    {
        parent::__construct($form, $label, $name);

        $this->type('checkbox');
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForHydrate(Request $request): mixed
    {
        return $request->boolean([$this->getKey()]);
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
     * {@inheritdoc}
     */
    public function data(Request $request): array
    {
        if (! $this->hasAttribute('checked')) {
            $this->checked(filter_var($this->resolveValue(), FILTER_VALIDATE_BOOL));
        }

        return parent::data($request);
    }
}
