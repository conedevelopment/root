<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Http\Requests\ShowRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Illuminate\Http\Request;

trait ResolvesVisibility
{
    /**
     * The visibility resolver callbacks.
     */
    protected array $visibilityResolvers = [];

    /**
     * Determine if the object is visible for the given request.
     */
    public function visible(Request $request): bool
    {
        foreach ($this->visibilityResolvers as $callback) {
            if (! call_user_func_array($callback, [$request])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Set the visibility hidden on index.
     */
    public function hiddenOnIndex(?Closure $callback = null): static
    {
        return $this->hiddenOn(static function (Request $request) use ($callback): bool {
            return $request instanceof IndexRequest
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility hidden on create.
     */
    public function hiddenOnCreate(?Closure $callback = null): static
    {
        return $this->hiddenOn(static function (Request $request) use ($callback): bool {
            return $request instanceof CreateRequest
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility hidden on show.
     */
    public function hiddenOnShow(?Closure $callback = null): static
    {
        return $this->hiddenOn(static function (Request $request) use ($callback): bool {
            return $request instanceof ShowRequest
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility hidden on update.
     */
    public function hiddenOnUpdate(?Closure $callback = null): static
    {
        return $this->hiddenOn(static function (Request $request) use ($callback): bool {
            return $request instanceof UpdateRequest
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility visible on index.
     */
    public function visibleOnIndex(?Closure $callback = null): static
    {
        return $this->visibleOn(static function (Request $request) use ($callback): bool {
            return $request instanceof IndexRequest
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility visible on create.
     */
    public function visibleOnCreate(?Closure $callback = null): static
    {
        return $this->visibleOn(static function (Request $request) use ($callback): bool {
            return $request instanceof CreateRequest
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility visible on show.
     */
    public function visibleOnShow(?Closure $callback = null): static
    {
        return $this->visibleOn(static function (Request $request) use ($callback): bool {
            return $request instanceof ShowRequest
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility visible on update.
     */
    public function visibleOnUpdate(?Closure $callback = null): static
    {
        return $this->visibleOn(static function (Request $request) use ($callback): bool {
            return $request instanceof UpdateRequest
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility hidden on index or show.
     */
    public function hiddenOnDisplay(?Closure $callback = null): static
    {
        return $this->hiddenOn(static function (Request $request) use ($callback): bool {
            return ($request instanceof IndexRequest || $request instanceof ShowRequest)
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility hidden on create or update.
     */
    public function hiddenOnForm(?Closure $callback = null): static
    {
        return $this->hiddenOn(static function (Request $request) use ($callback): bool {
            return ($request instanceof CreateRequest || $request instanceof UpdateRequest)
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility visible on index or show.
     */
    public function visibleOnDisplay(?Closure $callback = null): static
    {
        return $this->visibleOn(static function (Request $request) use ($callback): bool {
            return ($request instanceof IndexRequest || $request instanceof ShowRequest)
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility visible on create or update.
     */
    public function visibleOnForm(?Closure $callback = null): static
    {
        return $this->visibleOn(static function (Request $request) use ($callback): bool {
            return ($request instanceof CreateRequest || $request instanceof UpdateRequest)
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set a custom visibility resolver.
     */
    public function visibleOn(Closure $callback): static
    {
        $this->visibilityResolvers[] = $callback;

        return $this;
    }

    /**
     * Set a custom visibility resolver.
     */
    public function hiddenOn(Closure $callback): static
    {
        return $this->visibleOn(static function (Request $request) use ($callback): bool {
            return ! call_user_func_array($callback, [$request]);
        });
    }
}
