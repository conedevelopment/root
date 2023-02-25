<?php

namespace Cone\Root\Fields;

class Radio extends Select
{
    /**
     * The Vue component.
     */
    protected string $component = 'Radio';

    /**
     * Create a new field instance.
     *
     * @return void
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->type('radio');
    }
}
