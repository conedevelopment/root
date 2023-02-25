<?php

namespace Cone\Root\Fields;

use Closure;

class Number extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->type('number');
    }

    /**
     * Set the "min" attribute.
     */
    public function min(int|float|Closure $value): static
    {
        return $this->setAttribute('min', $value);
    }

    /**
     * Set the "max" attribute.
     */
    public function max(int|float|Closure $value): static
    {
        return $this->setAttribute('max', $value);
    }

    /**
     * Set the "step" attribute.
     */
    public function step(int|float|Closure $value): static
    {
        return $this->setAttribute('step', $value);
    }
}
