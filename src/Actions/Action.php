<?php

namespace Cone\Root\Actions;

use Closure;
use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Fields\Field;
use Cone\Root\Http\Controllers\ActionController;
use Cone\Root\Http\Requests\ActionRequest;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Support\Alert;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesFields;
use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

abstract class Action implements Arrayable, Responsable
{
    use Authorizable;
    use Makeable;
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
     * Indicates if the action is descrtuctive.
     *
     * @var bool
     */
    protected bool $destructive = false;

    /**
     * Indicates if the action is confirmable.
     *
     * @var bool
     */
    protected bool $confirmable = false;

    /**
     * Handle the action.
     *
     * @param  \Cone\Root\Http\Requests\ActionRequest  $request
     * @param  \Illuminate\Database\Eloquent\Collection  $models
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
     * Set the destructive property.
     *
     * @param  bool  $value
     * @return $this
     */
    public function destructive(bool $value = true): static
    {
        $this->destructive = $value;

        return $this;
    }

    /**
     * Determine if the action is destructive.
     *
     * @return bool
     */
    public function isDestructive(): bool
    {
        return $this->destructive;
    }

    /**
     * Set the confirmable property.
     *
     * @param  bool  $value
     * @return $this
     */
    public function confirmable(bool $value = true): static
    {
        $this->confirmable = $value;

        return $this;
    }

    /**
     * Determine if the action is confirmable.
     *
     * @return bool
     */
    public function isConfirmable(): bool
    {
        return $this->confirmable;
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
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Illuminate\Database\Eloquent\Builder
     *
     * @throws \Cone\Root\Exceptions\QueryResolutionException
     */
    public function resolveQuery(RootRequest $request): Builder
    {
        if (is_null($this->queryResolver)) {
            throw new QueryResolutionException();
        }

        return call_user_func_array($this->queryResolver, [$request]);
    }

    /**
     * Handle the resolving event on the field instance.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Cone\Root\Fields\Field  $field
     * @return void
     */
    protected function resolveField(RootRequest $request, Field $field): void
    {
        $field->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        });
    }

    /**
     * Register the action routes.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function registerRoutes(RootRequest $request, Router $router): void
    {
        $this->defaultRegisterRoutes($request, $router);

        $router->prefix($this->getKey())->group(function (Router $router) use ($request): void {
            $this->resolveFields($request)->registerRoutes($request, $router);
        });
    }

    /**
     * The routes that should be registered.
     *
     * @param  \Illuminate\Routing\Router  $router
     * @return void
     */
    public function routes(Router $router): void
    {
        $router->post('/', ActionController::class);
    }

    /**
     * Get the instance as an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'confirmable' => $this->isConfirmable(),
            'destructive' => $this->isDestructive(),
            'key' => $this->getKey(),
            'name' => $this->getName(),
            'url' => $this->getUri(),
        ];
    }

    /**
     * Get the form representation of the action.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toForm(RootRequest $request, Model $model): array
    {
        $fields = $this->resolveFields($request)
                        ->available($request, $model)
                        ->mapToForm($request, $model)
                        ->toArray();

        return array_merge($this->toArray(), [
            'data' => array_reduce($fields, static function (array $data, array $field): array {
                return array_replace_recursive($data, [$field['name'] => $field['value']]);
            }, []),
            'fields' => $fields,
        ]);
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request): Response
    {
        return Redirect::back()->with(
            sprintf('alerts.action-%s', $this->getKey()),
            Alert::info(__(':action was successful!' , ['action' => $this->getName()]))
        );
    }
}
