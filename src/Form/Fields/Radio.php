<?php

namespace Cone\Root\Form\Fields;

class Radio extends Checkbox
{
    /**
     * Make a new option instance.
     */
    public function newOption(string $label, mixed $value = null): RadioOption
    {
        return RadioOption::make($label, $value)->name($this->getAttribute('name'));
    }
}
