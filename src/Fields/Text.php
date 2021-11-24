<?php

namespace Cone\Root\Fields;

class Text extends Field
{
    /**
     * The field attributes.
     *
     * @var array
     */
    protected array $attributes = [
        'type' => 'text',
    ];

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
