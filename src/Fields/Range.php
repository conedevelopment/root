<?php

namespace Cone\Root\Fields;

class Range extends Field
{
    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'Range';

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

        $this->type('range');
    }
}
