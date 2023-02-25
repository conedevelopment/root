<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Http\Requests\ShowRequest;
use Cone\Root\Http\Requests\UpdateRequest;

trait ResolvesVisibility
{
    /**
     * The visibility resolver callbacks.
     */
    protected array $visibilityResolvers = [];

    /**
     * Determine if the object is visible for the given request.
     */
    public function visible(RootRequest $request): bool
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
     *
     * @return $this
     */
    public function hiddenOnIndex(?Closure $callback = null): static
    {
        return $this->hiddenOn(static function (RootRequest $request) use ($callback): bool {
            return $request instanceof IndexRequest
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility hidden on create.
     *
     * @return $this
     */
    public function hiddenOnCreate(?Closure $callback = null): static
    {
        return $this->hiddenOn(static function (RootRequest $request) use ($callback): bool {
            return $request instanceof CreateRequest
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility hidden on show.
     *
     * @return $this
     */
    public function hiddenOnShow(?Closure $callback = null): static
    {
        return $this->hiddenOn(static function (RootRequest $request) use ($callback): bool {
            return $request instanceof ShowRequest
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility hidden on update.
     *
     * @return $this
     */
    public function hiddenOnUpdate(?Closure $callback = null): static
    {
        return $this->hiddenOn(static function (RootRequest $request) use ($callback): bool {
            return $request instanceof UpdateRequest
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility visible on index.
     *
     * @return $this
     */
    public function visibleOnIndex(?Closure $callback = null): static
    {
        return $this->visibleOn(static function (RootRequest $request) use ($callback): bool {
            return $request instanceof IndexRequest
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility visible on create.
     *
     * @return $this
     */
    public function visibleOnCreate(?Closure $callback = null): static
    {
        return $this->visibleOn(static function (RootRequest $request) use ($callback): bool {
            return $request instanceof CreateRequest
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility visible on show.
     *
     * @return $this
     */
    public function visibleOnShow(?Closure $callback = null): static
    {
        return $this->visibleOn(static function (RootRequest $request) use ($callback): bool {
            return $request instanceof ShowRequest
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility visible on update.
     *
     * @return $this
     */
    public function visibleOnUpdate(?Closure $callback = null): static
    {
        return $this->visibleOn(static function (RootRequest $request) use ($callback): bool {
            return $request instanceof UpdateRequest
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility hidden on index or show.
     *
     * @return $this
     */
    public function hiddenOnDisplay(?Closure $callback = null): static
    {
        return $this->hiddenOn(static function (RootRequest $request) use ($callback): bool {
            return ($request instanceof IndexRequest || $request instanceof ShowRequest)
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility hidden on create or update.
     *
     * @return $this
     */
    public function hiddenOnForm(?Closure $callback = null): static
    {
        return $this->hiddenOn(static function (RootRequest $request) use ($callback): bool {
            return ($request instanceof CreateRequest || $request instanceof UpdateRequest)
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility visible on index or show.
     *
     * @return $this
     */
    public function visibleOnDisplay(?Closure $callback = null): static
    {
        return $this->visibleOn(static function (RootRequest $request) use ($callback): bool {
            return ($request instanceof IndexRequest || $request instanceof ShowRequest)
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set the visibility visible on create or update.
     *
     * @return $this
     */
    public function visibleOnForm(?Closure $callback = null): static
    {
        return $this->visibleOn(static function (RootRequest $request) use ($callback): bool {
            return ($request instanceof CreateRequest || $request instanceof UpdateRequest)
                && (is_null($callback) || call_user_func_array($callback, [$request]));
        });
    }

    /**
     * Set a custom visibility resolver.
     *
     * @return $this
     */
    public function visibleOn(Closure $callback): static
    {
        $this->visibilityResolvers[] = $callback;

        return $this;
    }

    /**
     * Set a custom visibility resolver.
     *
     * @return $this
     */
    public function hiddenOn(Closure $callback): static
    {
        return $this->visibleOn(static function (RootRequest $request) use ($callback): bool {
            return ! call_user_func_array($callback, [$request]);
        });
    }
}
