<?php

namespace Cone\Root\Fields;

class ID extends Field
{
    /**
     * Create a new field instance.
     *
     * @param  string|null  $label
     * @param  string|null  $name
     * @return void
     */
    public function __construct(?string $label = null, ?string $name = null)
    {
        parent::__construct($label ?: __('ID'), $name);
    }
}
