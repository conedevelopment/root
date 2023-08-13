<?php

namespace Cone\Root\Form\Fields;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;

class RelationOption extends Option implements Arrayable
{
    /**
     * The option label.
     */
    protected string $label;

    /**
     * The option model.
     */
    protected Model $model;

    /**
     * Create a new option instance.
     */
    public function __construct(Model $model, string $label)
    {
        $this->model = $model;

        parent::__construct($model->getKey(), $label);
    }

    /**
     * Get the array representation of the object.
     */
    public function toArray(): array
    {
        return $this->resolveAttributes();
    }
}
