<?php

namespace Cone\Root\Fields;

class Boolean extends Field
{
    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'Input';

    /**
     * The field attributes.
     *
     * @var array
     */
    protected array $attributes = [
        'type' => 'checkbox',
    ];
}
