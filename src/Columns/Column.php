<?php

namespace Cone\Root\Columns;

use Closure;
use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\ResolvesModelValue;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;

class Column
{
    use Conditionable;
    use HasAttributes;
    use Makeable;
    use ResolvesModelValue;

    /**
     * The label.
     */
    protected string $label;

    /**
     * The Blade template.
     */
    protected string $template = 'root::columns.cells.cell';

    /**
     * Indicates if the field is sortable.
     */
    protected bool|Closure $sortable = false;

    /**
     * Indicates if the field is searchable.
     */
    protected bool|Closure $searchable = false;

    /**
     * The format resolver callback.
     */
    protected ?Closure $formatResolver = null;

    /**
     * The value resolver callback.
     */
    protected ?Closure $valueResolver = null;

    /**
     * The associated model attribute.
     */
    protected string $modelAttribute;

    /**
     * The search query resolver callback.
     */
    protected ?Closure $searchQueryResolver = null;

    /**
     * Create a new column instance.
     */
    public function __construct(string $label, string $modelAttribute = null)
    {
        $this->label = $label;
        $this->modelAttribute = $modelAttribute ?: Str::of($label)->lower()->snake()->value();
    }

    /**
     * Get the model attribute.
     */
    public function getModelAttribute(): string
    {
        return $this->modelAttribute;
    }

    /**
     * Get the Blade template.
     */
    public function getTemplate(): string
    {
        return $this->template;
    }

    /**
     * Set the sortable attribute.
     */
    public function sortable(bool|Closure $value = true): static
    {
        $this->sortable = $value;

        return $this;
    }

    /**
     * Determine if the field is sortable.
     */
    public function isSortable(): bool
    {
        if ($this->sortable instanceof Closure) {
            return call_user_func($this->sortable);
        }

        return $this->sortable;
    }

    /**
     * Get the sort URL.
     */
    public function getSortUrl(Request $request): ?string
    {
        if (! $this->isSortable()) {
            return null;
        }

        return match ($request->input('sort.order', 'asc')) {
            'asc' => $request->fullUrlWithQuery(['sort' => ['order' => 'desc', 'sort' => $this->getModelAttribute()]]),
            default => $request->fullUrlWithQuery(['sort' => ['order' => 'asc', 'by' => $this->getModelAttribute()]]),
        };
    }

    /**
     * Set the searachable attribute.
     */
    public function searchable(bool|Closure $value = true): static
    {
        $this->searchable = $value;

        return $this;
    }

    /**
     * Determine if the field is searchable.
     */
    public function isSearchable(): bool
    {
        if ($this->searchable instanceof Closure) {
            return call_user_func($this->searchable);
        }

        return $this->searchable;
    }

    /**
     * Set the search query resolver.
     */
    public function searchWithQuery(Closure $callback): static
    {
        $this->searchQueryResolver = $callback;

        return $this;
    }

    /**
     * Resolve the serach query.
     */
    public function resolveSearchQuery(Request $request, Builder $query, mixed $value): Builder
    {
        return is_null($this->searchQueryResolver)
            ? $query
            : call_user_func_array($this->searchQueryResolver, [$request, $query, $value]);
    }

    /**
     * * Convert the column to a table head.
     */
    public function toHead(Request $request): array
    {
        return [
            'attribute' => $this->modelAttribute,
            'label' => $this->label,
            'sortable' => $this->isSortable(),
            'sortUrl' => $this->getSortUrl($request),
            'template' => 'root::columns.column',
        ];
    }

    /**
     * Convert the column to a cell.
     */
    public function toCell(Request $request, Model $model): array
    {
        return [
            'attrs' => $this->newAttributeBag(),
            'formattedValue' => $this->resolveFormat($request, $model),
            'model' => $model,
            'template' => $this->getTemplate(),
            'value' => $this->resolveValue($request, $model),
        ];
    }
}
