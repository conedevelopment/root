<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Closure;

class Number extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

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
