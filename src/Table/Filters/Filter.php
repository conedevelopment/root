<?php

namespace Cone\Root\Table\Filters;

use Cone\Root\Form\Fields\Field;
use Cone\Root\Table\Table;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\Makeable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

abstract class Filter
{
    use Authorizable;
    use Makeable;

    /**
     * The table instance.
     */
    protected Table $table;

    /**
     * Indicates whether the filter is functional.
     */
    protected bool $functional = false;

    /**
     * Create a new filter instance.
     */
    public function __construct(Table $table)
    {
        $this->table = $table;
    }

    /**
     * Apply the filter on the query.
     */
    abstract public function apply(Request $request, Builder $query, mixed $value): Builder;

    /**
     * Convert the filter to a form field.
     */
    abstract public function toField(FilterForm $form): Field;

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->kebab()->value();
    }

    /**
     * Get the name.
     */
    public function getName(): string
    {
        return __(Str::of(static::class)->classBasename()->headline()->value());
    }

    /**
     * The default value of the filter.
     */
    public function default(Request $request): mixed
    {
        return $request->query($this->getKey());
    }

    /**
     * Determine if the filter is active.
     */
    public function isActive(Request $request): bool
    {
        return ! empty($request->query($this->getKey()));
    }

    /**
     * Determine whether the filter is functional.
     */
    public function isFunctional(): bool
    {
        return $this->functional;
    }
}
