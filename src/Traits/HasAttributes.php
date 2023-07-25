<?php

namespace Cone\Root\Traits;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\View\ComponentAttributeBag;

trait HasAttributes
{
    /**
     * The field attributes.
     */
    protected array $attributes = [];

    /**
     * Get the attributes.
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }

    /**
     * Set the given attributes.
     */
    public function setAttributes(array $attributes): static
    {
        $this->attributes = array_replace($this->attributes, $attributes);

        return $this;
    }

    /**
     * Determine if the given attributes exists.
     */
    public function hasAttribute(string $key): bool
    {
        return Arr::has($this->attributes, $key);
    }

    /**
     * Get the given attribute.
     */
    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Set the given attribute.
     */
    public function setAttribute(string $key, mixed $value): static
    {
        Arr::set($this->attributes, $key, $value);

        return $this;
    }

    /**
     * Remove the given attribute.
     */
    public function removeAttribute(string $key): static
    {
        Arr::forget($this->attributes, $key);

        return $this;
    }

    /**
     * Remove the given attributes.
     */
    public function removeAttributes(array $keys): static
    {
        foreach ($keys as $key) {
            $this->removeAttribute($key);
        }

        return $this;
    }

    /**
     * Clear all the attributes.
     */
    public function clearAttributes(): static
    {
        $this->attributes = [];

        return $this;
    }

    /**
     * Resolve the attributes.
     */
    public function resolveAttributes(): array
    {
        return array_reduce(
            array_keys($this->attributes),
            function (array $attributes, string $key): mixed {
                return array_merge($attributes, [$key => $this->resolveAttribute($key)]);
            },
            []
        );
    }

    /**
     * Resolve the given attribute.
     */
    public function resolveAttribute(string $key): mixed
    {
        $value = $this->getAttribute($key);

        return $value instanceof Closure
                ? call_user_func($value)
                : $value;
    }

    /**
     * Make a new attribute bag instance.
     */
    public function newAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->resolveAttributes());
    }
}
