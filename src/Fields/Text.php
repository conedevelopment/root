<?php

namespace Cone\Root\Fields;

class Text extends Field
{
    /**
     * Create a new field instance.
     *
     * @param  string  $label
     * @param  string|null  $name
     * @return void
     */
    public function __construct(string $label, ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->type('text');
    }

    /**
     * Set the size attribute.
     *
     * @param  int  $value
     * @return $this
     */
    public function size(int $value): static
    {
        return $this->setAttribute('size', $value);
    }

    /**
     * Set the minlength attribute.
     *
     * @param  int  $value
     * @return $this
     */
    public function minlength(int $value): static
    {
        return $this->setAttribute('minlength', $value);
    }

    /**
     * Set the maxlength attribute.
     *
     * @param  int  $value
     * @return $this
     */
    public function maxlength(int $value): static
    {
        return $this->setAttribute('maxlength', $value);
    }
}
