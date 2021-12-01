<?php

namespace Cone\Root\Actions;

use Cone\Root\Http\Controllers\ActionController;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

abstract class Action implements Arrayable, Routable
{
    use ResolvesVisibility;

    /**
     * The cache store.
     *
     * @var array
     */
    protected array $cache = [];

    /**
     * The URI for the field.
     *
     * @var string|null
     */
    protected ?string $uri = null;

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
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Http\RedirectResponse
     */
    public function perform(Request $request, Builder $query): RedirectResponse
    {
        $this->handle(
            $request,
            $query->findMany($request->input('models', []))
        );

        return Redirect::back();
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
        return Str::of(static::class)->classBasename()->headline();
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
        if (! isset($this->cache['fields'])) {
            $this->cache['fields'] = Fields::make($this->fields($request));
        }

        return $this->cache['fields'];
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
            'url' => URL::to($this->uri),
        ];
    }

    /**
     * Register the routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function routes(Request $request): void
    {
        Route::post($this->getKey(), ActionController::class);
    }

    /**
     * Set the URI attribute.
     *
     * @param  string  $uri
     * @return void
     */
    public function setUri(?string $uri = null): void
    {
        $this->uri = $uri;
    }

    /**
     * Get the URI attribute.
     *
     * @return string|null
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }
}
