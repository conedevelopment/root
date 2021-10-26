<?php

namespace Cone\Root\Fields;

class Number extends Field
{
    /**
     * The field attributes.
     *
     * @var array
     */
    protected array $attributes = [
        'type' => 'number',
    ];

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
