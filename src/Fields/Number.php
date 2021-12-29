<?php

namespace Cone\Root\Fields;

class Number extends Field
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

        $this->type('number');
    }

    /**
     * Set the min attribute.
     *
     * @param  int  $value
     * @return $this
     */
    public function min(int $value): static
    {
        return $this->setAttribute('min', $value);
    }

    /**
     * Set the max attribute.
     *
     * @param  int  $value
     * @return $this
     */
    public function max(int $value): static
    {
        return $this->setAttribute('max', $value);
    }
}
