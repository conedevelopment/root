<?php

namespace Cone\Root\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use UnitEnum;

class Select extends Field
{
    /**
     * The Vue component.
     */
    protected string $component = 'Select';

    /**
     * The options resolver callback.
     */
    protected ?Closure $optionsResolver = null;

    /**
     * Indicates if the field should be nullable.
     */
    protected bool $nullable = false;

    /**
     * Set the nullable attribute.
     */
    public function nullable(bool $value = true): static
    {
        $this->nullable = $value;

        return $this;
    }

    /**
     * Determine if the field is nullable.
     */
    public function isNullable(): bool
    {
        return $this->nullable;
    }

    /**
     * Set the multiple attribute.
     */
    public function multiple(bool $value = true): static
    {
        return $this->setAttribute('multiple', $value);
    }

    /**
     * Set the size attribute.
     */
    public function size(int $value): static
    {
        return $this->setAttribute('size', $value);
    }

    /**
     * Set the options attribute.
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
     */
    public function resolveOptions(Request $request, Model $model): array
    {
        if (is_null($this->optionsResolver)) {
            return [];
        }

        $options = call_user_func_array($this->optionsResolver, [$request, $model]);

        return array_map(static function (mixed $formattedValue, int|string $value): array {
            return $formattedValue instanceof Option
                ? $formattedValue->toArray()
                : ['value' => $value, 'formattedValue' => $formattedValue];
        }, $options, array_keys($options));
    }

    /**
     * Format the value.
     */
    public function resolveFormat(Request $request, Model $model): mixed
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (Request $request, Model $model, mixed $value): mixed {
                $options = array_column(
                    $this->resolveOptions($request, $model), 'formattedValue', 'value'
                );

                $value = array_map(static function (mixed $value) use ($options): mixed {
                    $value = $value instanceof UnitEnum ? $value->value : $value;

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
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'nullable' => $this->isNullable(),
            'options' => $this->resolveOptions($request, $model),
        ]);
    }
}
