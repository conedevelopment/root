<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Filters\Filter;
use Cone\Root\Filters\RenderableFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

use function Illuminate\Support\enum_value;

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
        $name = match ($value) {
            true => $this->getAttribute('name').'[]',
            default => trim($this->getAttribute('name'), '[]'),
        };

        $this->setAttribute('name', $name);

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
                        $value = enum_value($value);

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
        $this->optionsResolver = is_array($value) ? static fn (): array => $value : $value;

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

            $option->selected(in_array(
                $option->getAttribute('value'),
                array_map(fn (mixed $v): mixed => enum_value($v), $value)
            ));

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

    /**
     * Get the filter representation of the field.
     */
    public function toFilter(): Filter
    {
        return new class($this) extends RenderableFilter
        {
            protected Select $field;

            public function __construct(Select $field)
            {
                parent::__construct($field->getModelAttribute());

                $this->field = $field;
            }

            public function apply(Request $request, Builder $query, mixed $value): Builder
            {
                return $this->field->resolveFilterQuery($request, $query, $value);
            }

            public function toField(): Field
            {
                return Select::make($this->field->getLabel(), $this->getRequestKey())
                    ->value(fn (Request $request): mixed => $this->getValue($request))
                    ->nullable()
                    ->options(function (Request $request, Model $model): array {
                        return array_column(
                            $this->field->resolveOptions($request, $model),
                            'label',
                            'value',
                        );
                    });
            }
        };
    }
}
