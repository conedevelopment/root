<?php

namespace Cone\Root\Filters;

use Cone\Root\Fields\Field;

abstract class RenderableFilter extends Filter
{
    /**
     * Convert the filter to a form field.
     */
    abstract public function toField(): Field;
}
