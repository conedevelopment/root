<?php

namespace Cone\Root\Support;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Stringable;

class ClassList implements Arrayable, Stringable
{
    /**
     * The class list items.
     */
    protected array $classes = [];

    /**
     * Create a new class list instance.
     */
    public function __construct(array $classes = [])
    {
        $this->classes = $classes;
    }

    /**
     * Add new classes to the class list.
     */
    public function add(string|array $value): static
    {
        $this->classes = array_values(array_unique(
            array_merge($this->classes, (array) $value)
        ));

        return $this;
    }

    /**
     * Remove classes from the class list.
     */
    public function remove(string|array $value): static
    {
        $this->classes = array_values(
            array_diff($this->classes, (array) $value)
        );

        return $this;
    }

    /**
     * Replace the given values in the class list.
     */
    public function replace(string $old, string $new): static
    {
        $index = array_search($old, $this->classes);

        if ($index !== false) {
            $this->classes[$index] = $new;
        }

        return $this;
    }

    /**
     * Toggle a class in the class list.
     */
    public function toggle(string $value, ?bool $force = null): static
    {
        if (is_null($force)) {
            $this->contains($value) ? $this->remove($value) : $this->add($value);
        } elseif ($force) {
            $this->add($value);
        } else {
            $this->remove($value);
        }

        return $this;
    }

    /**
     * Determine whether the class is present in the class list.
     */
    public function contains(string $value): bool
    {
        return in_array($value, $this->classes);
    }

    /**
     * Clear the class list.
     */
    public function clear(): static
    {
        $this->classes = [];

        return $this;
    }

    /**
     * Convert the class list to an array.
     */
    public function toArray(): array
    {
        return array_values(array_unique($this->classes));
    }

    /**
     * Convert the class list to a string.
     */
    public function __toString(): string
    {
        return Arr::toCssClasses($this->toArray());
    }
}
