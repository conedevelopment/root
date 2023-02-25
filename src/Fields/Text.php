<?php

namespace Cone\Root\Fields;

use Closure;

class Text extends Field
{
    /**
     * Create a new field instance.
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

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
