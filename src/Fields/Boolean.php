<?php

namespace Cone\Root\Fields;

class Boolean extends Field
{
    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'Checkbox';

    /**
     * Create a new file field instance.
     *
     * @param  string  $label
     * @param  string|null  $name
     * @return void
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->type('checkbox');
    }
}
