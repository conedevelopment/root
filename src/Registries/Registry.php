<?php

namespace Cone\Root\Registries;

use ArrayAccess;
use ArrayIterator;
use Cone\Root\Interfaces\Registries\Item;
use Illuminate\Contracts\Support\Arrayable;
use IteratorAggregate;

abstract class Registry implements Arrayable, ArrayAccess, IteratorAggregate
{
    /**
     * The registry items.
     *
     * @var array
     */
    protected array $items = [];

    /**
     * Register an item into the registry.
     *
     * @param  string  $key
     * @param  \Cone\Root\Interfaces\Registries\Item  $item
     * @return void
     */
    public function register(string $key, Item $item): void
    {
        $this->offsetSet($key, $item);
    }

    /**
     * Remove an item from the registry.
     *
     * @param  string  $key
     * @return void
     */
    public function remove(string $key): void
    {
        $this->offsetUnset($key);
    }

    /**
     * Get the registry items.
     *
     * @return array
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Get the given registry item.
     *
     * @param  string  $key
     * @return mixed
     */
    public function get(string $key): mixed
    {
        return $this->offsetExists($key) ? $this->offsetGet($key) : null;
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  string  $key
     * @return bool
     */
    public function has(string $key): bool
    {
        return $this->offsetExists($key);
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  mixed  $key
     * @return bool
     */
    public function offsetExists(mixed $key): bool
    {
        return isset($this->items[$key]);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $key
     * @return mixed
     */
    public function offsetGet(mixed $key): mixed
    {
        return $this->items[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet(mixed $key, mixed $value): void
    {
        if (is_null($key)) {
            $this->items[] = $value;
        } else {
            $this->items[$key] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset(mixed $key): void
    {
        unset($this->items[$key]);
    }

    /**
     * Get an iterator for the items.
     *
     * @return \ArrayIterator
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return array_map(static function (object $item): mixed {
            return $item instanceof Arrayable ? $item->toArray() : $item;
        }, $this->items);
    }
}
