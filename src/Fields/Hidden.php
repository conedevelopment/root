<?php

namespace Cone\Root\Fields;

use Closure;

class Hidden extends Field
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.hidden';

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->type('hidden');
    }
}
