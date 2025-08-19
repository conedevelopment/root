<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Closure;

class Color extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct(string $label, Closure|string|null $modelAttribute = null)
    {
        parent::__construct($label, $modelAttribute);

        $this->type('color');
    }
}
