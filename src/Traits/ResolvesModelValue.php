<?php

namespace Cone\Root\Traits;

use Closure;
use Illuminate\Database\Eloquent\Model;

trait ResolvesModelValue
{
    /**
     * The format resolver callback.
     */
    protected ?Closure $formatResolver = null;

    /**
     * The value resolver callback.
     */
    protected ?Closure $valueResolver = null;

    /**
     * Resolve the model.
     */
    abstract public function resolveModel(): Model;

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
    public function resolveValue(): mixed
    {
        $value = $this->getValue();

        if (is_null($this->valueResolver)) {
            return $value;
        }

        return call_user_func_array($this->valueResolver, [$this->resolveModel(), $value]);
    }

    /**
     * Get the default value from the model.
     */
    public function getValue(): mixed
    {
        return $this->resolveModel()->getAttribute($this->getKey());
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
    public function resolveFormat(): mixed
    {
        $value = $this->resolveValue();

        if (is_null($this->formatResolver)) {
            return $value;
        }

        return call_user_func_array($this->formatResolver, [$this->resolveModel(), $value]);
    }
}
