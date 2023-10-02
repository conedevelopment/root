<?php

namespace Cone\Root\Fields;

use Cone\Root\Fields\Options\CheckboxOption;

class Checkbox extends Select
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.checkbox';

    /**
     * Make a new option instance.
     */
    public function newOption(mixed $value, string $label): CheckboxOption
    {
        return CheckboxOption::make($value, $label)->name(sprintf('%s[]', $this->getModelAttribute()));
    }
}
