<?php

namespace Cone\Root\Settings;

use ArrayAccess;
use Cone\Root\Interfaces\Settings\Repository as Contract;
use Cone\Root\Models\Setting;
use Illuminate\Contracts\Support\Arrayable;

class Repository implements Arrayable, ArrayAccess, Contract
{
    /**
     * The repository cache.
     */
    protected array $cache = [];

    /**
     * Get the setting model.
     */
    public function model(): Setting
    {
        return Setting::proxy();
    }

    /**
     * Get the value for the given key.
     */
    public function get(string $key, mixed $default = null, bool $fresh = false): mixed
    {
        if ($this->offsetExists($key) && ! $fresh) {
            return $this->offsetGet($key);
        }

        $model = $this->model()->newQuery()->firstWhere('key', '=', $key);

        if (! is_null($model)) {
            $this->offsetSet($key, $model->value);
        }

        return $this->cache[$key] ?? $default;
    }

    /**
     * Set the value for the given key.
     */
    public function set(string $key, mixed $value): void
    {
        $model = $this->model()->newQuery()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        $this->offsetSet($key, $model->value);
    }

    /**
     * Delete the given keys.
     */
    public function delete(string|array $keys): void
    {
        foreach ((array) $keys as $key) {
            $this->offsetUnset($key);
        }

        $this->model()->newQuery()->whereIn('key', (array) $keys)->delete();
    }

    /**
     * Flush the cache.
     */
    public function flush(): void
    {
        $this->cache = [];
    }

    /**
     * Convert the repository to an array.
     */
    public function toArray(): array
    {
        return $this->cache;
    }

    /**
     * Determine if an item exists at an offset.
     *
     * @param  TKey  $key
     */
    public function offsetExists($key): bool
    {
        return isset($this->cache[$key]);
    }

    /**
     * Get an item at a given offset.
     *
     * @param  TKey  $key
     */
    public function offsetGet($key): mixed
    {
        return $this->cache[$key];
    }

    /**
     * Set the item at a given offset.
     *
     * @param  TKey|null  $key
     * @param  TValue  $value
     */
    public function offsetSet($key, $value): void
    {
        if (is_null($key)) {
            $this->cache[] = $value;
        } else {
            $this->cache[$key] = $value;
        }
    }

    /**
     * Unset the item at a given offset.
     *
     * @param  TKey  $key
     */
    public function offsetUnset($key): void
    {
        unset($this->cache[$key]);
    }
}
