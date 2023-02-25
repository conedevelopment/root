<?php

namespace Cone\Root\Fields;

use Closure;

class Number extends Field
{
    /**
     * Create a new field instance.
     *
     * @return void
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->type('number');
    }

    /**
     * Set the "min" attribute.
     *
     * @return $this
     */
    public function min(int|float|Closure $value): static
    {
        return $this->setAttribute('min', $value);
    }

    /**
     * Set the "max" attribute.
     *
     * @return $this
     */
    public function max(int|float|Closure $value): static
    {
        return $this->setAttribute('max', $value);
    }

    /**
     * Set the "step" attribute.
     *
     * @return $this
     */
    public function step(int|float|Closure $value): static
    {
        return $this->setAttribute('step', $value);
    }
}
