<?php

namespace Cone\Root\Table\Filters;

use Cone\Root\Table\Table;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\Makeable;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

abstract class Filter implements Renderable
{
    use Authorizable;
    use Makeable;

    /**
     * The Blade template.
     */
    protected ?string $template = 'root::table.filters.select';

    /**
     * Indicates if multiple options can be selected.
     */
    protected bool $multiple = false;

    /**
     * The table insance.
     */
    protected Table $table;

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
        $default = $request->query($this->getKey());

        return $this->multiple ? Arr::wrap($default) : $default;
    }

    /**
     * Determine if the filter is active.
     */
    public function active(Request $request): bool
    {
        return ! empty($request->query($this->getKey()));
    }

    /**
     * Determine if the filter is functional.
     */
    public function functional(): bool
    {
        return is_null($this->template);
    }

    /**
     * Get the filter options.
     */
    public function options(Request $request): array
    {
        return [];
    }

    /**
     * Set the multiple attribute.
     */
    public function multiple(bool $value = true): static
    {
        $this->multiple = $value;

        return $this;
    }
}
