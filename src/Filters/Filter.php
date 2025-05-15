<?php

namespace Cone\Root\Filters;

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
     * The filter key.
     */
    protected string $key;

    /**
     * Create a new filter instance.
     */
    public function __construct(?string $key = null)
    {
        $this->key = $key ?: Str::of(static::class)->classBasename()->snake()->value();
    }

    /**
     * Apply the filter on the query.
     */
    abstract public function apply(Request $request, Builder $query, mixed $value): Builder;

    /**
     * Set the filter key.
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * Get the request key.
     */
    public function getRequestKey(): string
    {
        return $this->getKey();
    }

    /**
     * Get the name.
     */
    public function getName(): string
    {
        return __(Str::of(static::class)->classBasename()->headline()->value());
    }

    /**
     * Get the value of the filter.
     */
    public function getValue(Request $request): mixed
    {
        return $request->input($this->getRequestKey());
    }

    /**
     * Determine if the filter is active.
     */
    public function isActive(Request $request): bool
    {
        return $request->filled($this->getRequestKey());
    }
}
