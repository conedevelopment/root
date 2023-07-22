<?php

namespace Cone\Root\Form\Fields;

class RadioOption extends CheckboxOption
{
    /**
     * Create a new option instance.
     */
    public function __construct(string $label, mixed $value = null)
    {
        parent::__construct($label, $value);

        $this->setAttribute('type', 'radio');
    }
}
