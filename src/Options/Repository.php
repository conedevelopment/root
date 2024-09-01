<?php

namespace Cone\Root\Options;

use Cone\Root\Interfaces\Options\Repository as Contract;
use Cone\Root\Models\Option;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Repository implements Contract
{
    /**
     * The option cache.
     */
    protected Collection $cache;

    /**
     * The the casts.
     */
    protected array $casts = [];

    /**
     * Create a new repository instance.
     */
    public function __construct()
    {
        $this->cache = new Collection;
    }

    /**
     * Get the option query.
     */
    public function query(): Builder
    {
        return Option::proxy()->newQuery();
    }

    /**
     * Get the option.
     */
    public function get(string $key, mixed $default = null, bool $refresh = false): mixed
    {
        if (! $refresh && $this->cache->has($key)) {
            return $this->cache->get($key);
        }

        $option = $this->query()->where('key', $key)->first();

        $value = match (true) {
            ! is_null($option) => $option->value,
            default => $default,
        };

        $this->cache->put($key, $value);

        return $value;
    }

    /**
     * Get the options.
     */
    public function getMany(array $keys, array $defaults = [], bool $refresh = false): Collection
    {
        $cache = $this->cache->whereIn('key', $refresh ? $keys : []);

        $options = $this->query()->whereIn('key',  $cache->diffKeys($keys))->get();

        return Collection::make($defaults)
            ->merge($cache)
            ->merge($options->pluck('value', 'key')->all());
    }

    /**
     * Set the option.
     */
    public function set(string $key, mixed $value): void
    {
        $this->query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );

        $this->cache->put($key, $value);
    }

    /**
     * Set the options.
     */
    public function setMany(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value);
        }
    }

    /**
     * Delete the option.
     */
    public function delete(string $key): void
    {
        $this->query()->where('key', $key)->delete();

        $this->cache->forget($key);
    }

    /**
     * Delete the options.
     */
    public function deleteMany(array $keys): void
    {
        $this->query()->whereIn('key', $keys)->delete();

        $this->cache->forget($keys);
    }

    /**
     * Set the cast to the given key.
     */
    public function cast(string $key, string $cast): static
    {
        $this->casts[$key] = $cast;

        return $this;
    }

    /**
     * Resolve the cast for the given key.
     */
    public function resolveCast(string $key): string
    {
        return $this->casts[$key] ?? 'string';
    }
}
