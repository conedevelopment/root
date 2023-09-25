<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Form;

class Text extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct(Form $form, string $label, string $modelAttribute = null)
    {
        parent::__construct($form, $label, $modelAttribute);

        $this->type('text');
    }

    /**
     * Set the "size" HTML attribute.
     */
    public function size(int|Closure $value): static
    {
        return $this->setAttribute('size', $value);
    }

    /**
     * Set the "minlength" HTML attribute.
     */
    public function minlength(int|Closure $value): static
    {
        return $this->setAttribute('minlength', $value);
    }

    /**
     * Set the "maxlength" HTML attribute.
     */
    public function maxlength(int|Closure $value): static
    {
        return $this->setAttribute('maxlength', $value);
    }
}
