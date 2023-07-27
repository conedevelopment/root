<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Form;

class Number extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct(Form $form, string $label, string $key = null)
    {
        parent::__construct($form, $label, $key);

        $this->type('number');
    }

    /**
     * Set the "min" HTML attribute.
     */
    public function min(int|float|Closure $value): static
    {
        return $this->setAttribute('min', $value);
    }

    /**
     * Set the "max" HTML attribute.
     */
    public function max(int|float|Closure $value): static
    {
        return $this->setAttribute('max', $value);
    }

    /**
     * Set the "step" HTML attribute.
     */
    public function step(int|float|Closure $value): static
    {
        return $this->setAttribute('step', $value);
    }
}
