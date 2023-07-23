<?php

namespace Cone\Root\Form\Fields;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class Select extends Field
{
    /**
     * The blade template.
     */
    protected string $template = 'root::form.fields.select';

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
    public function resolveOptions(): array
    {
        if (is_null($this->optionsResolver)) {
            return [];
        }

        $options = call_user_func_array($this->optionsResolver, [$this->resolveModel()]);

        $value = Arr::wrap($this->resolveValue());

        return array_map(function (mixed $label, mixed $option) use ($value): Option {
            $option = $label instanceof Option ? $label : $this->newOption($label, $option);

            $option->selected(in_array($option->getAttribute('value'), $value));

            return $option;
        }, $options, array_keys($options));
    }

    /**
     * Make a new option instance.
     */
    public function newOption(string $label, mixed $value = null): Option
    {
        return new Option($label, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function data(Request $request): array
    {
        return array_merge(parent::data($request), [
            'nullable' => $this->isNullable(),
            'options' => $this->resolveOptions(),
        ]);
    }
}