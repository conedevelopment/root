<?php

namespace Cone\Root\Traits;

use Illuminate\Support\Arr;

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
     *
     * @return $this
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
     *
     * @param  mixed  $default
     */
    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->attributes, $key, $default);
    }

    /**
     * Set the given attribute.
     *
     * @return $this
     */
    public function setAttribute(string $key, mixed $value): static
    {
        Arr::set($this->attributes, $key, $value);

        return $this;
    }

    /**
     * Remove the given attribute.
     *
     * @return $this
     */
    public function removeAttribute(string $key): static
    {
        Arr::forget($this->attributes, $key);

        return $this;
    }

    /**
     * Remove the given attributes.
     *
     * @return $this
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
     *
     * @return $this
     */
    public function clearAttributes(): static
    {
        $this->attributes = [];

        return $this;
    }
}
