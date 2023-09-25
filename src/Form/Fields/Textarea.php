<?php

namespace Cone\Root\Form\Fields;

use Closure;

class Textarea extends Field
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.textarea';

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
