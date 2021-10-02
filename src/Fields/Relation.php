<?php

namespace Cone\Root\Fields;

abstract class Relation extends Field
{
    /**
     * Indicates if the field should be nullable.
     *
     * @var bool
     */
    protected bool $nullable = false;

    /**
     * Set the nullable attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function nullable(bool $value = true): self
    {
        $this->nullable = $value;

        return $this;
    }
}
