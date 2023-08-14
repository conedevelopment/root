<?php

namespace Cone\Root\Form\Fields;

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
     * Create a new option instance.
     */
    public function __construct(Model $model, string $label)
    {
        $this->model = $model;

        parent::__construct($model->getKey(), $label);
    }
}
