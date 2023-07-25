<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Form\Fields\Fields;

trait ResolvesFields
{
    /**
     * The fields instance.
     */
    public readonly Fields $fields;

    /**
     * Define the fields for the object.
     */
    public function fields(): array
    {
        return [];
    }

    /**
     * Apply the callback on the fields.
     */
    public function withFields(Closure $callback): static
    {
        call_user_func_array($callback, [$this->fields]);

        return $this;
    }
}
