<?php

namespace Cone\Root\Fields;

class Color extends Field
{
    /**
     * Create a new field instance.
     *
     * @return void
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->type('color');
    }
}
