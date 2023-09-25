<?php

namespace Cone\Root\Form\Fields\Options;

use Cone\Root\Form\Fields\Fields;
use Illuminate\Database\Eloquent\Model;

class RelationOption extends Option
{
    /**
     * The option label.
     */
    protected string $label;

    /**
     * The option model.
     */
    public readonly Model $model;

    /**
     * The pivot fields.
     */
    protected ?Fields $fields = null;

    /**
     * Create a new option instance.
     */
    public function __construct(Model $model, string $label)
    {
        $this->model = $model;

        parent::__construct($model->getKey(), $label);
    }

    /**
     * Set the pivot fields.
     */
    public function withPivotFields(Fields $fields): static
    {
        $this->fields = $fields;

        return $this;
    }
}
