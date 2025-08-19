<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Closure;

class Range extends Number
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.range';

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->type('range');
        $this->step(1);
        $this->min(0);
        $this->max(100);
        $this->class(['form-range', 'range-group__control']);
    }
}
