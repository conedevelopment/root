<?php

namespace Cone\Root\Fields;

class Radio extends Select
{
    /**
     * The Vue component.
     *
     * @var string
     */
    protected string $component = 'Radio';

    /**
     * Create a new field instance.
     *
     * @param  string  $label
     * @param  string|null  $name
     * @return void
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->type('radio');
    }
}
