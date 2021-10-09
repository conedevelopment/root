<?php

namespace Cone\Root\Fields;

class ID extends Field
{
    /**
     * The field attributes.
     *
     * @var array
     */
    protected array $attributes = [
        'label' => 'ID',
    ];

    /**
     * Indicates if the field is UUID.
     *
     * @var bool
     */
    protected bool $uuid = false;

    /**
     * Mark the field as UUID.
     *
     * @param  bool  $value
     * @return $this
     */
    public function uuid(bool $value = true): self
    {
        $this->uuid = $value;

        return $this;
    }
}
