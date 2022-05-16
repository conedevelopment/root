<?php

namespace Cone\Root\Filters;

use Cone\Root\Http\Requests\RootRequest;
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
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    abstract public function options(RootRequest $request): array;

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
    public function default(RootRequest $request): mixed
    {
        $default = parent::default($request);

        return $this->multiple ? Arr::wrap($default) : $default;
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request): array
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
