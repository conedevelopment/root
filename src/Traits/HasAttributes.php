<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Support\ClassList;
use Illuminate\Support\Arr;
use Illuminate\View\ComponentAttributeBag;

trait HasAttributes
{
    /**
     * The field attributes.
     */
    protected array $attributes = [];

    /**
     * Set the "id" HTML attribute.
     */
    public function id(string $value): static
    {
        return $this->setAttribute('id', strtolower($value));
    }

    /**
     * Add a "class" HTML attribute.
     */
    public function class(string|array $value): static
    {
        $this->classList()->add($value);

        return $this;
    }

    /**
     * Get the class list.
     */
    public function classList(): ClassList
    {
        if (! isset($this->attributes['class'])) {
            $this->attributes['class'] = new ClassList;
        }

        return $this->attributes['class'];
    }

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
        foreach ($attributes as $key => $value) {
            $this->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Determine if the given attributes exists.
     */
    public function hasAttribute(string $key): bool
    {
        return array_key_exists($key, $this->attributes);
    }

    /**
     * Get the given attribute.
     */
    public function getAttribute(string $key, mixed $default = null): mixed
    {
        return match ($key) {
            'class' => $this->classList()->__toString(),
            default => $this->attributes[$key] ?? $default,
        };
    }

    /**
     * Set the given attribute.
     */
    public function setAttribute(string $key, mixed $value): static
    {
        match ($key) {
            'class' => $this->classList()->clear()->add($value),
            default => $this->attributes[$key] = $value,
        };

        return $this;
    }

    /**
     * Remove the given attribute.
     */
    public function removeAttribute(string $key): static
    {
        unset($this->attributes[$key]);

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
            function (array $attributes, string $key): array {
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

        $value = $value instanceof Closure ? call_user_func_array($value, [$this]) : $value;

        return match ($key) {
            'class' => (string) $value,
            'style' => Arr::toCssStyles((array) $value),
            default => $value,
        };
    }

    /**
     * Make a new attribute bag instance.
     */
    public function newAttributeBag(): ComponentAttributeBag
    {
        return new ComponentAttributeBag($this->resolveAttributes());
    }
}
