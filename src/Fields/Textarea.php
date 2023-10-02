<?php

namespace Cone\Root\Fields;

use Closure;

class Textarea extends Field
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.textarea';

    /**
     * Set the rows attribute.
     */
    public function rows(int|Closure $value): static
    {
        return $this->setAttribute('rows', $value);
    }

    /**
     * Set the cols attribute.
     */
    public function cols(int|Closure $value): static
    {
        return $this->setAttribute('cols', $value);
    }
}
