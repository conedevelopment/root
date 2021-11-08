<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;

trait ResolvesVisibility
{
    /**
     * The visibility resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $visibilityResolver = null;

    /**
     * Determine if the field is visible for the given request and action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string|null  $action
     * @return bool
     */
    public function visible(Request $request, ?string $action = null): bool
    {
        return is_null($this->visibilityResolver)
            || call_user_func_array($this->visibilityResolver, [$request, $action]);
    }

    /**
     * Set the visibility hidden on index.
     *
     * @param  \Closure|null  $callback
     * @return $this
     */
    public function hiddenOnIndex(?Closure $callback = null): static
    {
        return $this->hiddenOn(static function (Request $request, ?string $action = null) use ($callback): bool {
            return $action === Resource::INDEX
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
        return $this->hiddenOn(static function (Request $request, ?string $action = null) use ($callback): bool {
            return $action === Resource::CREATE
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
        return $this->hiddenOn(static function (Request $request, ?string $action = null) use ($callback): bool {
            return $action === Resource::SHOW
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
        return $this->hiddenOn(static function (Request $request, ?string $action = null) use ($callback): bool {
            return $action === Resource::UPDATE
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
        return $this->visibleOn(static function (Request $request, ?string $action = null) use ($callback): bool {
            return $action === Resource::INDEX
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
        return $this->visibleOn(static function (Request $request, ?string $action = null) use ($callback): bool {
            return $action === Resource::CREATE
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
        return $this->visibleOn(static function (Request $request, ?string $action = null) use ($callback): bool {
            return $action === Resource::SHOW
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
        return $this->visibleOn(static function (Request $request, ?string $action = null) use ($callback): bool {
            return $action === Resource::UPDATE
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
        return $this->hiddenOn(static function (Request $request, ?string $action = null) use ($callback): bool {
            return in_array($action, [Resource::INDEX, Resource::SHOW])
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
        return $this->hiddenOn(static function (Request $request, ?string $action = null) use ($callback): bool {
            return in_array($action, [Resource::UPDATE, Resource::CREATE])
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
        return $this->visibleOn(static function (Request $request, ?string $action = null) use ($callback): bool {
            return in_array($action, [Resource::INDEX, Resource::SHOW])
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
        return $this->visibleOn(static function (Request $request, ?string $action = null) use ($callback): bool {
            return in_array($action, [Resource::CREATE, Resource::UPDATE])
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
        $this->visibilityResolver = $callback;

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
        return $this->visibleOn(static function (Request $request, ?string $action = null) use ($callback): bool {
            return ! call_user_func_array($callback, [$request, $action]);
        });
    }
}
