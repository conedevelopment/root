<?php

namespace Cone\Root\Filters;

abstract class SelectFilter extends Filter
{
    /**
     * Indicates if mulitple options can be selected.
     *
     * @var bool
     */
    protected bool $multiple = false;

    /**
     * Set the multiple attribute.
     *
     * @return void
     */
    public function multiple(bool $value = true): self
    {
        $this->multiple = $value;

        return $this;
    }
}
