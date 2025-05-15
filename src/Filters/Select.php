<?php

namespace Cone\Root\Filters;

use Cone\Root\Fields\Select as Field;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\App;

abstract class Select extends RenderableFilter
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
    public function getValue(Request $request): mixed
    {
        $default = parent::getValue($request);

        return $this->isMultiple() ? Arr::wrap($default) : $default;
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
     * Determine if the filter is multiple.
     */
    public function isMultiple(): bool
    {
        return $this->multiple;
    }

    /**
     * {@inheritdoc}
     */
    public function toField(): Field
    {
        return Field::make($this->getName(), $this->getRequestKey())
            ->options(App::call(fn (Request $request): array => $this->options($request)))
            ->value(fn (Request $request): mixed => $this->getValue($request))
            ->multiple($this->isMultiple());
    }
}
