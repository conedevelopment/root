<?php

namespace Cone\Root\Actions;

use Closure;
use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Forms\Form;
use Cone\Root\Http\Controllers\ActionController;
use Cone\Root\Interfaces\HasForm;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Support\Alert;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesFields;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

abstract class Action implements Arrayable, HasForm, Responsable, Routable
{
    use Makeable;
    use RegistersRoutes;
    use ResolvesFields;

    /**
     * The query resolver callback.
     */
    protected ?Closure $queryResolver = null;

    /**
     * Indicates if the action is descrtuctive.
     */
    protected bool $destructive = false;

    /**
     * Indicates if the action is confirmable.
     */
    protected bool $confirmable = false;

    /**
     * Handle the action.
     */
    abstract public function handle(Request $request, Collection $models): void;

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return Str::of(static::class)->classBasename()->kebab()->value();
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return $this->getKey();
    }

    /**
     * Get the name.
     */
    public function getName(): string
    {
        return __(Str::of(static::class)->classBasename()->headline()->value());
    }

    /**
     * Set the destructive property.
     */
    public function destructive(bool $value = true): static
    {
        $this->destructive = $value;

        return $this;
    }

    /**
     * Determine if the action is destructive.
     */
    public function isDestructive(): bool
    {
        return $this->destructive;
    }

    /**
     * Set the confirmable property.
     */
    public function confirmable(bool $value = true): static
    {
        $this->confirmable = $value;

        return $this;
    }

    /**
     * Determine if the action is confirmable.
     */
    public function isConfirmable(): bool
    {
        return $this->confirmable;
    }

    /**
     * Perform the action.
     */
    public function perform(Request $request): Response
    {
        $query = $this->resolveQuery($request);

        // toForm

        $this->handle(
            $request,
            $request->boolean('all') ? $query->get() : $query->findMany($request->input('models', []))
        );

        return $this->toResponse($request);
    }

    /**
     * Set the query resolver.
     *
     * @return $this
     */
    public function withQuery(Closure $callback): static
    {
        $this->queryResolver = $callback;

        return $this;
    }

    /**
     * Resolve the query for the extract.
     */
    public function resolveQuery(Request $request): Builder
    {
        if (is_null($this->queryResolver)) {
            throw new QueryResolutionException();
        }

        return call_user_func_array($this->queryResolver, [$request]);
    }

    /**
     * The routes that should be registered.
     */
    public function routes(Router $router): void
    {
        $router->post('/', ActionController::class);
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return [
            'confirmable' => $this->isConfirmable(),
            'destructive' => $this->isDestructive(),
            'key' => $this->getKey(),
            'name' => $this->getName(),
        ];
    }

    /**
     * Convert the resource to a form.
     */
    public function toForm(Request $request, Model $model): Form
    {
        return new Form(
            $model,
            $this->resolveFields($request)->authorized($request, $model)
        );
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Cone\Root\Http\Requests\Request  $request
     */
    public function toResponse($request): Response
    {
        return Redirect::back()->with(
            sprintf('alerts.action-%s', $this->getKey()),
            Alert::info(__(':action was successful!', ['action' => $this->getName()]))
        );
    }
}
