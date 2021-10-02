<?php

namespace Cone\Root\Fields;

class ID extends Field
{
    /**
     * Create a new field instance.
     *
     * @param  string  $label
     * @param  string|null  $name
     * @return void
     */
    public function __construct(string $label = 'ID', ?string $name = null)
    {
        parent::__construct($label, $name);
    }
}
