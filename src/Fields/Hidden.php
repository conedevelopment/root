<?php

declare(strict_types = 1);

namespace Cone\Root\Fields;

class Hidden extends Field
{
    /**
     * The Vue component.
     *
     * @var string
     */
    protected string $component = 'Hidden';

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

        $this->type('hidden');
    }
}
