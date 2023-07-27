<?php

namespace Cone\Root\Form\Fields;

class Checkbox extends Select
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.checkbox';

    /**
     * Make a new option instance.
     */
    public function newOption(mixed $value, string $label): CheckboxOption
    {
        return CheckboxOption::make($value, $label)->name(sprintf('%s[]', $this->getKey()));
    }
}
