<?php

namespace Cone\Root\Fields;

use Cone\Root\Traits\ResolvesFields;

class Fieldset extends Field
{
    use ResolvesFields;

    /**
     * The Vue component.
     */
    protected string $component = 'Fieldset';
}
