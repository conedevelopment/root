<?php

namespace Cone\Root\Resources;

use Closure;
use Cone\Root\Traits\HasAttributes;
use Illuminate\Database\Eloquent\Model;

abstract class ModelValueHandler
{
    use HasAttributes;

    /**
     * The label.
     */
    protected string $label;

    /**
     * The format resolver callback.
     */
    protected ?Closure $formatResolver = null;

    /**
     * The value resolver callback.
     */
    protected ?Closure $valueResolver = null;

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
}