<?php

namespace Cone\Root\Fields;

class ID extends Field
{
    /**
     * Create a new field instance.
     *
     * @param  string  $label
     * @param  string|null  $name
     * @return void
     */
    public function __construct(string $label = 'ID', ?string $name = null)
    {
        parent::__construct($label, $name);

        $this->visibleOnDisplay();
    }

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
    public function uuid(bool $value = true): static
    {
        $this->uuid = $value;

        return $this;
    }
}
