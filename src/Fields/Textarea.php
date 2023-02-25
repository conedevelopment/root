<?php

namespace Cone\Root\Fields;

use Closure;

class Textarea extends Field
{
    /**
     * The Vue component.
     */
    protected string $component = 'Textarea';

    /**
     * Set the rows attribute.
     *
     * @return $this
     */
    public function rows(int|Closure $value): static
    {
        return $this->setAttribute('rows', $value);
    }

    /**
     * Set the cols attribute.
     *
     * @return $this
     */
    public function cols(int|Closure $value): static
    {
        return $this->setAttribute('cols', $value);
    }
}
