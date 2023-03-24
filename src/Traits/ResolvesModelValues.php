<?php

namespace Cone\Root\Traits;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

trait ResolvesModelValues
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
        if (is_null($this->valueResolver)) {
            $this->valueResolver = fn (): mixed => $model->getAttribute($this->name);
        }

        return call_user_func_array($this->valueResolver, [$request, $model]);
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
}
