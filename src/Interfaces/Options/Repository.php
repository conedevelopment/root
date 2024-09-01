<?php

namespace Cone\Root\Interfaces\Options;

use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

interface Repository
{
    /**
     * Get the option query.
     */
    public function query(): Builder;

    /**
     * Get the option.
     */
    public function get(string $key, mixed $default = null, bool $refresh = false): mixed;

    /**
     * Get the options.
     */
    public function getMany(array $keys, array $defaults = [], bool $refresh = false): Collection;

    /**
     * Set the option.
     */
    public function set(string $key, mixed $value): void;

    /**
     * Set the options.
     */
    public function setMany(array $values): void;

    /**
     * Delete the option.
     */
    public function delete(string $key): void;

    /**
     * Delete the options.
     */
    public function deleteMany(array $keys): void;

    /**
     * Set the cast to the given key.
     */
    public function cast(string $key, string $cast): static;

    /**
     * Resolve the cast for the given key.
     */
    public function resolveCast(string $key): string;
}
