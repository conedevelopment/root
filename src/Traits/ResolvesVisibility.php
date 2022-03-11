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
     *
     * @var array
     */
    protected array $visibilityResolvers = [];

    /**
     * Determine if the field is visible for the given request and action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
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
     *
     * @param  \Closure|null  $callback
     * @return $this
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
     *
     * @param  \Closure|null  $callback
     * @return $this
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
     *
     * @param  \Closure|null  $callback
     * @return $this
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
     *
     * @param  \Closure|null  $callback
     * @return $this
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
     *
     * @param  \Closure|null  $callback
     * @return $this
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
     *
     * @param  \Closure|null  $callback
     * @return $this
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
     *
     * @param  \Closure|null  $callback
     * @return $this
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
     *
     * @param  \Closure|null  $callback
     * @return $this
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
     *
     * @param  \Closure|null  $callback
     * @return $this
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
     *
     * @param  \Closure|null  $callback
     * @return $this
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
     *
     * @param  \Closure|null  $callback
     * @return $this
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
     *
     * @param  \Closure|null  $callback
     * @return $this
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
     *
     * @param  \Closure  $callback
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
     * @param  \Closure  $callback
     * @return $this
     */
    public function hiddenOn(Closure $callback): static
    {
        return $this->visibleOn(static function (Request $request) use ($callback): bool {
            return ! call_user_func_array($callback, [$request]);
        });
    }
}
