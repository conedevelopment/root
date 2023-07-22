<?php

namespace Cone\Root\Form\Fields;

class CheckboxOption extends Option
{
    /**
     * The blade template.
     */
    protected string $template = 'root::form.fields.checkbox-option';

    /**
     * Create a new option instance.
     */
    public function __construct(string $label, mixed $value = null)
    {
        parent::__construct($label, $value);

        $this->setAttribute('type', 'checkbox');
    }

    /**
     * Set the "name" HTML attribute.
     */
    public function name(string $value): static
    {
        return $this->setAttribute('name', $value);
    }

    /**
     * Set the "selected" HTML attribute.
     */
    public function selected(bool $value = true): static
    {
        return $this->checked($value);
    }

    /**
     * Set the "checked" HTML attribute.
     */
    public function checked(bool $value = true): static
    {
        return $this->setAttribute('checked', $value);
    }
}
