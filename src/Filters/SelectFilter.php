<?php

namespace Cone\Root\Filters;

use Illuminate\Http\Request;
use Illuminate\Support\Arr;

abstract class SelectFilter extends Filter
{
    /**
     * The Vue component.
     *
     * @var string|null
     */
    protected ?string $component = 'Select';

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
    public function default(Request $request): mixed
    {
        $default = parent::default($request);

        return $this->multiple ? Arr::wrap($default) : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request): array
    {
        $options = $this->options($request);

        return array_merge(parent::toArray(), [
            'nullable' => true,
            'multiple' => $this->multiple,
            'options' => array_map(static function (mixed $value, mixed $key): array {
                return [
                    'value' => $key,
                    'formatted_value' => $value,
                ];
            }, $options, array_keys($options)),
        ]);
    }
}
