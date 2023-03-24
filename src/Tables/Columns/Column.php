<?php

namespace Cone\Root\Tables\Columns;

use Closure;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class Column implements Arrayable
{
    use Makeable;

    /**
     * The column label.
     */
    protected string $label;

    /**
     * The column name.
     */
    protected string $name;

    /**
     * Indicates if the field is sortable.
     */
    protected bool $sortable = false;

    /**
     * Indicates if the field is searchable.
     */
    protected bool $searchable = false;

    /**
     * The format resolver callback.
     */
    protected ?Closure $formatResolver = null;

    /**
     * The value resolver callback.
     */
    protected ?Closure $valueResolver = null;

    /**
     * Create a new column instance.
     */
    public function __construct(string $label, string $name = null)
    {
        $this->label($label);
        $this->name($name ?: Str::of($label)->lower()->snake()->value());
    }

    /**
     * Set the label attribute.
     */
    public function label(string $value): static
    {
        $this->label = $value;

        return $this;
    }

    /**
     * Set the name attribute.
     */
    public function name(string $value): static
    {
        $this->name = $value;

        return $this;
    }

    /**
     * Set the sortable attribute.
     */
    public function sortable(bool $value = true): static
    {
        $this->sortable = $value;

        return $this;
    }

    /**
     * Determine if the field is sortable.
     */
    public function isSortable(): bool
    {
        return $this->sortable;
    }

    /**
     * Set the searachable attribute.
     */
    public function searchable(bool $value = true): static
    {
        $this->searchable = $value;

        return $this;
    }

    /**
     * Determine if the field is searchable.
     */
    public function isSearchable(): bool
    {
        return $this->searchable;
    }

    /**
     * Set the value resolver.
     */
    public function value(Closure $callback): static
    {
        $this->valueResolver = $callback;

        return $this;
    }

    /**
     * Resolve the value.
     */
    public function resolveValue(Request $request, Model $model): mixed
    {
        $value = $model->getAttribute($this->name);

        if (is_null($this->valueResolver)) {
            return $value;
        }

        return call_user_func_array($this->valueResolver, [$request, $model, $value]);
    }

    /**
     * Set the format resolver.
     */
    public function format(Closure $callback): static
    {
        $this->formatResolver = $callback;

        return $this;
    }

    /**
     * Format the value.
     */
    public function resolveFormat(Request $request, Model $model): mixed
    {
        $value = $this->resolveValue($request, $model);

        if (is_null($this->formatResolver)) {
            return $value;
        }

        return call_user_func_array($this->formatResolver, [$request, $model, $value]);
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'name' => $this->name,
        ];
    }

    /**
     * Display the column.
     */
    public function toDisplay(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'id' => $model->getKey(),
            'value' => $this->resolveValue($request, $model),
            'formatted_value' => $this->resolveFormat($request, $model),
            'searchable' => $this->isSearchable(),
            'sortable' => $this->isSortable(),
        ]);
    }
}
