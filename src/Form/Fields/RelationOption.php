<?php

namespace Cone\Root\Form\Fields;

use Closure;
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
     * Resolve the pivot fields.
     */
    public function withPivotFields(Closure $callback): static
    {
        $this->fields = call_user_func_array($callback, [$this->model]);

        return $this;
    }
}
