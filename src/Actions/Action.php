<?php

namespace Cone\Root\Actions;

use Closure;
use Cone\Root\Resources\Resource;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

abstract class Action implements Arrayable
{
    /**
     * The visibility resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $visibilityResolver = null;

    /**
     * Make a new action instance.
     *
     * @param  array  ...$parameters
     * @return static
     */
    public static function make(...$parameters): static
    {
        return new static(...$parameters);
    }

    /**
     * Handle the action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Support\Collection  $models
     * @return void
     */
    abstract public function handle(Request $request, Collection $models): void;

    /**
     * Perform the action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Resources\Resource  $resource
     * @return \Illuminate\Http\RedirectResponse
     */
    public function perform(Request $request, Resource $resource): RedirectResponse
    {
        $models = $resource->filteredQuery($request, $resource->resolveFilters($request))
                            ->findMany($request->input('models', []));

        $this->handle($request, $models);

        return Redirect::back()->with('message', __('Action performed!'));
    }

    /**
     * Get the key for the filter.
     *
     * @return string
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->lower()->kebab();
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return Str::of(static::class)->classBasename();
    }

    /**
     * Define the fields for the action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [];
    }

    /**
     * Resolve the fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Fields
     */
    public function resolveFields(Request $request): Fields
    {
        return Fields::make($this->fields($request));
    }

    /**
     * Determine if the field is visible for the given request and action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $action
     * @return bool
     */
    public function visible(Request $request, string $action): bool
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
        return $this->hiddenOn(static function (Request $request, string $action) use ($callback): bool {
            return $action === Resource::INDEX
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
        return $this->hiddenOn(static function (Request $request, string $action) use ($callback): bool {
            return $action === Resource::SHOW
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
        return $this->visibleOn(static function (Request $request, string $action) use ($callback): bool {
            return $action === Resource::INDEX
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
        return $this->visibleOn(static function (Request $request, string $action) use ($callback): bool {
            return $action === Resource::SHOW
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
        return $this->hiddenOn(static function (Request $request, string $action) use ($callback): bool {
            return in_array($action, [Resource::INDEX, Resource::SHOW])
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
        return $this->visibleOn(static function (Request $request, string $action) use ($callback): bool {
            return in_array($action, [Resource::INDEX, Resource::SHOW])
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
        return $this->visibleOn(static function (Request $request, string $action) use ($callback): bool {
            return ! call_user_func_array($callback, [$request, $action]);
        });
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'key' => $this->getKey(),
            'name' => $this->getName(),
        ];
    }
}
