<?php

namespace Cone\Root\Form\Fields;

class ID extends Field
{
    /**
     * Indicates if the field is UUID.
     */
    protected bool $uuid = false;

    /**
     * Create a new field instance.
     */
    public function __construct(string $label = 'ID', ?string $name = null)
    {
        parent::__construct($label, $name);
    }

    /**
     * Mark the field as UUID.
     */
    public function uuid(bool $value = true): static
    {
        $this->uuid = $value;

        return $this;
    }

    /**
     * Determine if the field is UUID.
     */
    public function isUuid(): bool
    {
        return $this->uuid;
    }
}
