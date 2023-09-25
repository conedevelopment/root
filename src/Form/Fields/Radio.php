<?php

namespace Cone\Root\Form\Fields;

use Cone\Root\Form\Fields\Options\RadioOption;

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
