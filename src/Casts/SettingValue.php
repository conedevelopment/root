<?php

namespace Cone\Root\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;

class SettingValue implements CastsAttributes
{
    /**
     * The setting key.
     */
    protected string $key;

    /**
     * Create a new controller instance.
     */
    public function __construct(string $key)
    {
        $this->key = $key;
    }

    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set(Model $model, string $key, mixed $value, array $attributes): mixed
    {
        return $value;
    }
}
