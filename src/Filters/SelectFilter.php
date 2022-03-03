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
    protected string $component = 'Select';

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
     * The default value of the filter.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    public function default(Request $request): mixed
    {
        $default = parent::default($request);

        if ($this->multiple) {
            return $default ?: [];
        }

        return $default;
    }

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
        $options = App::call([$this, 'options']);

        return array_merge(parent::toArray(), [
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
