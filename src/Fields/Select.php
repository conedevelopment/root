<?php

namespace Cone\Root\Fields;

use BackedEnum;
use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class Select extends Field
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.select';

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
     * Set the "multiple" HTML attribute.
     */
    public function multiple(bool $value = true): static
    {
        return $this->setAttribute('multiple', $value);
    }

    /**
     * Set the "size" HTML attribute.
     */
    public function size(int $value): static
    {
        return $this->setAttribute('size', $value);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveFormat(Request $request, Model $model): ?string
    {
        if (is_null($this->formatResolver)) {
            $this->formatResolver = function (Request $request, Model $model, mixed $value): string {
                $options = array_column(
                    $this->resolveOptions($request, $model), 'label', 'value'
                );

                return Collection::make($value)
                    ->map(static function (mixed $value) use ($options): string {
                        $value = $value instanceof BackedEnum ? $value->value : $value;

                        return $options[$value] ?? $value;
                    })
                    ->implode(', ');
            };
        }

        return parent::resolveFormat($request, $model);
    }

    /**
     * Set the options attribute.
     */
    public function options(array|Closure $value): static
    {
        $this->optionsResolver = is_callable($value) ? $value : static function () use ($value): array {
            return $value;
        };

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

        $value = Arr::wrap($this->resolveValue($request, $model));

        return array_map(function (mixed $label, mixed $option) use ($value): array {
            $option = $label instanceof Option ? $label : $this->newOption($option, $label);

            $option->selected(in_array($option->getAttribute('value'), $value));

            return $option->toArray();
        }, $options, array_keys($options));
    }

    /**
     * Make a new option instance.
     */
    public function newOption(mixed $value, string $label): Option
    {
        return new Option($value, $label);
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
