<?php

namespace Cone\Root\Form\Fields;

class PivotFields extends Fields
{
    /**
     * Add a new field to the collection.
     */
    public function field(string $field, string $label, string $key = null, ...$params): Field
    {
        $instance = parent::field($field, $label, $key, ...$params);

        //

        return $instance;
    }
}
