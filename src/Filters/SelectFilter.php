<?php

namespace Cone\Root\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

abstract class SelectFilter extends Filter
{
    /**
     * The Vue component.
     *
     * @var string
     */
    protected string $component = 'SelectFilter';

    /**
     * Indicates if mulitple options can be selected.
     *
     * @var bool
     */
    protected bool $multiple = false;

    /**
     * Get the filter options.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    abstract public function options(Request $request): array;

    /**
     * Set the multiple attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function multiple(bool $value = true): static
    {
        $this->multiple = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'multiple' => $this->multiple,
            'options' => App::call([$this, 'options']),
        ]);
    }
}
