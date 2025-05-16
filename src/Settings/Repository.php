<?php

namespace Cone\Root\Settings;

use ArrayAccess;
use Cone\Root\Interfaces\Settings\Repository as Contract;
use Cone\Root\Models\Setting;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;

/**
 * @template TKey of array-key
 * @template TValue
 */
class Repository implements Arrayable, ArrayAccess, Contract
{
    /**
     * The repository cache.
     */
    protected array $cache = [];

    /**
     * The value casts.
     */
    protected array $casts = [];

    /**
     * Get the setting model.
     */
    public function model(): Setting
    {
        return Setting::proxy();
    }

    /**
     * Get the base query for the repository.
     */
    public function query(): Builder
    {
        return $this->model()->newQuery();
    }

    /**
     * Set the value cast.
     */
    public function cast(string $key, string $type): void
    {
        $this->casts[$key] = $type;
    }

    /**
     * Merge the casts.
     */
    public function mergeCasts(array $casts): void
    {
        $this->casts = array_merge($this->casts, $casts);
    }

    /**
     * Remove the given casts.
     */
    public function removeCasts(string|array $keys): void
    {
        foreach ((array) $keys as $key) {
            unset($this->casts[$key]);
        }
    }

    /**
     * Remove the given casts.
     */
    public function clearCasts(): void
    {
        $this->casts = [];
    }

    /**
     * Get the value casts.
     */
    public function getCasts(): array
    {
        return $this->casts;
    }

    /**
     * Get the value for the given key.
     */
    public function get(string $key, mixed $default = null, bool $fresh = false): mixed
    {
        if (! $fresh && $this->offsetExists($key)) {
            return $this->offsetGet($key);
        }

        return $this->refresh($key, $default);
    }

    /**
     * Refresh the given key.
     */
    public function refresh(string $key, mixed $default = null): mixed
    {
        $model = $this->query()->firstWhere('key', '=', $key);

        $value = is_null($model)
            ? $default
            : $model->castValue($this->casts[$key] ?? null)->value;

        $this->offsetSet($key, $value);

        return $value;
    }

    /**
     * Set the value for the given key.
     */
    public function set(string $key, mixed $value): mixed
    {
        $model = $this->query()->firstOrNew(['key' => $key]);

        $model->castValue($this->casts[$key] ?? null);

        $model->fill(['value' => $value]);

        $model->save();

        $this->offsetSet($key, $model->value);

        return $this->offsetGet($key);
    }

    /**
     * Delete the given keys.
     */
    public function delete(string|array $keys): void
    {
        foreach ((array) $keys as $key) {
            $this->offsetUnset($key);
        }

        $this->query()->whereIn('key', (array) $keys)->delete();
    }

    /**
     * Flush the cache.
     */
    public function flush(): void
    {
        $this->cache = [];
    }

    /**
     * Get all the settings.
     */
    public function all(): array
    {
        return $this->toArray();
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
