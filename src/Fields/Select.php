<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

class Select extends Field
{
    /**
     * The Vue component.
     *
     * @var string
     */
    protected string $component = 'Select';

    /**
     * The options resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $optionsResolver = null;

    /**
     * Indicates if the field should be nullable.
     *
     * @var bool
     */
    protected bool $nullable = false;

    /**
     * Set the nullable attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function nullable(bool $value = true): static
    {
        $this->nullable = $value;

        return $this;
    }

    /**
     * Determine if the field is nullable.
     *
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * Set the multiple attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function multiple(bool $value = true): static
    {
        return $this->setAttribute('multiple', $value);
    }

    /**
     * Set the options attribute.
     *
     * @param  array|\Closure  $value
     * @return $this
     */
    public function options(array|Closure $value): static
    {
        if (is_array($value)) {
            $value = static function () use ($value): array {
                return $value;
            };
        }

        $this->optionsResolver = $value;

        return $this;
    }

    /**
     * Resolve the options for the field.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function resolveOptions(RootRequest $request, Model $model): array
    {
        if (is_null($this->optionsResolver)) {
            return [];
        }

        $options = call_user_func_array($this->optionsResolver, [$request, $model]);

        return array_map(static function (mixed $formattedValue, string $value): array {
            return [
                'value' => $value,
                'formatted_value' => $formattedValue,
            ];
        }, $options, array_keys($options));
    }

    /**
     * Format the value.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function resolveFormat(RootRequest $request, Model $model): mixed
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (RootRequest $request, Model $model, mixed $value): mixed {
                $options = array_column(
                    $this->resolveOptions($request, $model), 'formatted_value', 'value'
                );

                $value = array_map(static function (mixed $value) use ($options): mixed {
                    return $options[$value] ?? $value;
                }, Arr::wrap($value));

                return implode(', ', $value);
            };
        }

        return parent::resolveFormat($request, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'nullable' => $this->nullable,
            'options' => $this->resolveOptions($request, $model),
        ]);
    }
}
