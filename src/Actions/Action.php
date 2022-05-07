<?php

namespace Cone\Root\Actions;

use Closure;
use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Http\Controllers\ActionController;
use Cone\Root\Http\Requests\ActionRequest;
use Cone\Root\Support\Alert;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesFields;
use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

abstract class Action implements Arrayable, Responsable
{
    use Authorizable;
    use ResolvesFields;
    use ResolvesVisibility;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as defaultRegisterRoutes;
    }

    /**
     * The query resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $queryResolver = null;

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
     * @param  \Cone\Root\Http\Requests\ActionRequest  $request
     * @param  \Illuminate\Support\Collection  $models
     * @return void
     */
    abstract public function handle(ActionRequest $request, Collection $models): void;

    /**
     * Get the key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->kebab()->toString();
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return __(Str::of(static::class)->classBasename()->headline()->toString());
    }

    /**
     * Perform the action.
     *
     * @param  \Cone\Root\Http\Requests\ActionRequest  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function perform(ActionRequest $request): Response
    {
        $query = $this->resolveQuery($request);

        $model = $query->getModel();

        $request->validate(
            $this->resolveFields($request)
                ->available($request, $model)
                ->mapToValidate($request, $model)
        );

        $this->handle(
            $request,
            $request->boolean('all') ? $query->get() : $query->findMany($request->input('models', []))
        );

        return $this->toResponse($request);
    }

    /**
     * Set the query resolver.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function withQuery(Closure $callback): static
    {
        $this->queryResolver = $callback;

        return $this;
    }

    /**
     * Resolve the query for the extract.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Cone\Root\Exceptions\QueryResolutionException
     */
    public function resolveQuery(Request $request): Builder
    {
        if (is_null($this->queryResolver)) {
            throw new QueryResolutionException();
        }

        return call_user_func_array($this->queryResolver, [$request]);
    }

    /**
     * Register the action routes.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $this->defaultRegisterRoutes($request, $router);

        $router->prefix($this->getKey())->group(function (Router $router) use ($request): void {
            $this->resolveFields($request)->registerRoutes($request, $router);
        });
    }

    /**
     * The routes that should be registerd.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function routes(Router $router): void
    {
        $router->post($this->getKey(), ActionController::class);
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
            'url' => URL::to($this->getUri()),
        ];
    }

    /**
     * Get the form representation of the action.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toForm(Request $request, Model $model): array
    {
        $fields = $this->resolveFields($request)->available($request, $model)->mapToForm($request, $model)->toArray();

        return array_merge($this->toArray(), [
            'data' => array_column($fields, 'value', 'name'),
            'fields' => $fields,
        ]);
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request): Response
    {
        return Redirect::back()->with(
            "alerts.action-{$this->getKey()}",
            Alert::info(__(':action was successful!' , ['action' => $this->getName()]))
        );
    }
}
