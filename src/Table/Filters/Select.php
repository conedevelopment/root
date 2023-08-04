<?php

namespace Cone\Root\Table\Filters;

use Cone\Root\Form\Fields\Select as Field;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

abstract class Select extends Filter
{
    /**
     * Indicates if multiple options can be selected.
     */
    protected bool $multiple = false;

    /**
     * Get the filter options.
     */
    abstract public function options(Request $request): array;

    /**
     * {@inheritdoc}
     */
    public function default(Request $request): mixed
    {
        $default = parent::default($request);

        return $this->multiple ? Arr::wrap($default) : $default;
    }

    /**
     * Set the multiple attribute.
     */
    public function multiple(bool $value = true): static
    {
        $this->multiple = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toField(FilterForm $form): Field
    {
        return Field::make($form, $this->getName(), $this->getKey())
            ->options(App::call(function (Request $request): array {
                return $this->options($request);
            }))
            ->multiple($this->multiple);
    }
}
