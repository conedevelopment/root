<?php

namespace Cone\Root\Fields;

class Repeater extends Meta
{
    /**
     * The Vue component.
     */
    protected string $component = 'Repeater';

    protected string $key;

    /**
     * Create a new repeater field instance.
     */
    public function __construct(string $label, string $key = null, string $name = 'metas')
    {
        parent::__construct($label, $name);

        $this->key = $key;
    }
}
