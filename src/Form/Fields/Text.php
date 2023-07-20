<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Cone\Root\Form\Form;

class Text extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct(Form $form, string $label, string $name = null)
    {
        parent::__construct($form, $label, $name);

        $this->type('text');
    }

    /**
     * Set the size attribute.
     *
     * @return $this
     */
    public function size(int|Closure $value): static
    {
        return $this->setAttribute('size', $value);
    }

    /**
     * Set the minlength attribute.
     *
     * @return $this
     */
    public function minlength(int|Closure $value): static
    {
        return $this->setAttribute('minlength', $value);
    }

    /**
     * Set the maxlength attribute.
     *
     * @return $this
     */
    public function maxlength(int|Closure $value): static
    {
        return $this->setAttribute('maxlength', $value);
    }
}
