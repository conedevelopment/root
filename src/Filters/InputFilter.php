<?php

namespace Cone\Root\Filters;

abstract class InputFilter extends Filter
{
    /**
     * The Vue component.
     *
     * @var string|null
     */
    protected ?string $component = 'Input';
}
