<?php

namespace Cone\Root\Fields;

use Closure;

class Email extends Text
{
    /**
     * Create a new field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->type('email');
    }
}
