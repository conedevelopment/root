<?php

declare(strict_types=1);

namespace Cone\Root\Interfaces\Settings;

use Cone\Root\Models\Setting;

interface Repository
{
    /**
     * Get the setting model.
     */
    public function model(): Setting;

    /**
     * Set the value cast.
     */
    public function cast(string $key, string $type): void;

    /**
     * Merge the casts.
     */
    public function mergeCasts(array $casts): void;

    /**
     * Remove the given casts.
     */
    public function removeCasts(string|array $keys): void;

    /**
     * Remove the given casts.
     */
    public function clearCasts(): void;

    /**
     * Get the value casts.
     */
    public function getCasts(): array;

    /**
     * Get the value for the given key.
     */
    public function get(string $key, mixed $default = null, bool $fresh = false): mixed;

    /**
     * Set the value for the given key.
     */
    public function set(string $key, mixed $value): mixed;

    /**
     * Delete the given keys.
     */
    public function delete(string|array $keys): void;

    /**
     * Flush the cache.
     */
    public function flush(): void;

    /**
     * Get all the settings.
     */
    public function all(): array;
}
