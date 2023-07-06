<?php

namespace Cone\Root\Resources;

use Closure;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\HasAttributes;
use Cone\Root\Traits\Makeable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
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
    public function resolveValue(Request $request, Model $model): mixed
    {
        $value = $this->getValue($model);

        if (is_null($this->valueResolver)) {
            return $value;
        }

        return call_user_func_array($this->valueResolver, [$request, $model, $value]);
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
    public function resolveFormat(Request $request, Model $model): mixed
    {
        $value = $this->resolveValue($request, $model);

        if (is_null($this->formatResolver)) {
            return $value;
        }

        return call_user_func_array($this->formatResolver, [$request, $model, $value]);
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
