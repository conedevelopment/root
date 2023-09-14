<?php

namespace Cone\Root\Form\Fields\Options;

class RadioOption extends CheckboxOption
{
    /**
     * Create a new option instance.
     */
    public function __construct(mixed $value, string $label)
    {
        parent::__construct($value, $label);

        $this->setAttribute('type', 'radio');
    }
}
