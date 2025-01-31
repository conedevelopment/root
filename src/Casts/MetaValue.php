<?php

namespace Cone\Root\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use Stringable;

class MetaValue implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return is_null($value) ? $value : (json_decode($value, true) ?? $value);
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): string|false|null
    {
        return match (true) {
            is_null($value) => null,
            is_string($value), is_numeric($value), $value instanceof Stringable => (string) $value,
            default => json_encode($value) ?: (string) $value,
        };
    }
}
