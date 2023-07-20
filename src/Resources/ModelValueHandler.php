<?php

namespace Cone\Root\Resources;

use Closure;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

abstract class ModelValueHandler
{
    use Authorizable;
    use HasAttributes;
    use Makeable;

    /**
     * The format resolver callback.
     */
    protected ?Closure $formatResolver = null;

    /**
     * The value resolver callback.
     */
    protected ?Closure $valueResolver = null;

    /**
     * Create a new field instance.
     */
    public function __construct(string $label, string $name = null)
    {
        $this->setAttribute('label', $label);
        $this->setAttribute('name', $name ??= Str::of($label)->lower()->snake()->value());
    }

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return $this->getAttribute('name');
    }

    /**
     * Set the value resolver.
     */
    public function value(Closure $callback): static
    {
        $this->valueResolver = $callback;

        return $this;
    }

    /**
     * Resolve the value.
     */
    public function resolveValue(Model $model): mixed
    {
        $value = $this->getValue($model);

        if (is_null($this->valueResolver)) {
            return $value;
        }

        return call_user_func_array($this->valueResolver, [$model, $value]);
    }

    /**
     * Get the default value from the model.
     */
    public function getValue(Model $model): mixed
    {
        return $model->getAttribute($this->getKey());
    }

    /**
     * Set the format resolver.
     */
    public function format(Closure $callback): static
    {
        $this->formatResolver = $callback;

        return $this;
    }

    /**
     * Format the value.
     */
    public function resolveFormat(Model $model): mixed
    {
        $value = $this->resolveValue($model);

        if (is_null($this->formatResolver)) {
            return $value;
        }

        return call_user_func_array($this->formatResolver, [$model, $value]);
    }

    /**
     * Resolve the attributes.
     */
    public function resolveAttributes(Model $model): array
    {
        return array_reduce(
            array_keys($this->attributes),
            function (array $attributes, string $key) use ($model): mixed {
                return array_merge($attributes, [$key => $this->resolveAttribute($model, $key)]);
            },
            []
        );
    }

    /**
     * Resolve the given attribute.
     */
    public function resolveAttribute(Model $model, string $key): mixed
    {
        $value = $this->getAttribute($key);

        return $value instanceof Closure
                ? call_user_func_array($value, [$model])
                : $value;
    }

    /**
     * Set the given attribute.
     */
    public function __set(string $key, mixed $value): void
    {
        $this->setAttribute($key, $value);
    }

    /**
     * Get the given attribute.
     */
    public function __get(string $key): mixed
    {
        return $this->getAttribute($key);
    }

    /**
     * Determine if the given attribute exists.
     */
    public function __isset(string $key): bool
    {
        return $this->hasAttribute($key);
    }

    /**
     * Remove the given attribute.
     */
    public function __unset(string $key): void
    {
        $this->removeAttribute($key);
    }
}
