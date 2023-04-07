<?php

namespace Cone\Root\Traits;

trait ManagesContext
{
    /**
     * The context of the object.
     */
    protected ?string $context = null;

    /**
     * Set the context.
     */
    public function setContext(?string $context = null): static
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get the context.
     */
    public function getContext(): ?string
    {
        return $this->context;
    }
}
