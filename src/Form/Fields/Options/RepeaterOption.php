<?php

namespace Cone\Root\Form\Fields\Options;

use Cone\Root\Form\Fields\Fields;
use Illuminate\Database\Eloquent\Model;

class RepeaterOption extends Option
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::form.fields.repeater-option';

    /**
     * The option model.
     */
    public readonly Model $model;

    /**
     * The fields.
     */
    protected ?Fields $fields = null;

    /**
     * Indicates if the option should be open.
     */
    protected bool $open = true;

    /**
     * Create a new option instance.
     */
    public function __construct(Model $model, string $label)
    {
        $this->model = $model;

        parent::__construct($model->getAttribute('_key'), $label);
    }

    /**
     * Set the open attribute.
     */
    public function open(bool $value = true): static
    {
        $this->open = $value;

        return $this;
    }

    /**
     * Set the open attribute.
     */
    public function closed(): static
    {
        return $this->open(false);
    }

    /**
     * Resolve the fields.
     */
    public function withFields(Fields $fields): static
    {
        $this->fields = $fields;

        return $this;
    }

        /**
     * Get the array representation of the object.
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'open' => $this->open,
            'fields' => $this->fields?->all() ?: [],
        ]);
    }
}
