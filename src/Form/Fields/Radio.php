<?php

namespace Cone\Root\Form\Fields;

class Radio extends Checkbox
{
    /**
     * Make a new option instance.
     */
    public function newOption(mixed $value, string $label): RadioOption
    {
        return RadioOption::make($value, $label)->name($this->getModelAttribute());
    }
}
