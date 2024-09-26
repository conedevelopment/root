<?php

namespace Cone\Root\Actions;

use Closure;
use Cone\Root\Exceptions\QueryResolutionException;
use Cone\Root\Exceptions\SaveFormDataException;
use Cone\Root\Fields\Field;
use Cone\Root\Fields\Relation;
use Cone\Root\Http\Controllers\ActionController;
use Cone\Root\Http\Middleware\Authorize;
use Cone\Root\Interfaces\Form;
use Cone\Root\Support\Alert;
use Cone\Root\Traits\AsForm;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\HasRootEvents;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesVisibility;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Concerns\HasAttributes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\MessageBag;
use Illuminate\Support\Str;
use JsonSerializable;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

abstract class Action implements Arrayable, Form, JsonSerializable
{
    use AsForm;
    use Authorizable;
    use HasAttributes;
    use Makeable;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }
    use ResolvesVisibility;

    /**
     * The Blade template.
     */
    protected string $template = 'root::actions.action';

    /**
     * Indicates if the action is descrtuctive.
     */
    protected bool $destructive = false;

    /**
     * Indicates if the action is confirmable.
     */
    protected bool $confirmable = false;

    /**
     * Indicates if the action is standalone.
     */
    protected bool $standalone = false;

    /**
     * The query resolver.
     */
    protected ?Closure $queryResolver = null;

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
     * Get the modal key.
     */
    public function getModalKey(): string
    {
        return sprintf('action-%s', $this->getKey());
    }

    /**
     * Resolve the query.
     */
    public function resolveQuery(Request $request): Builder
    {
        if (is_null($this->queryResolver)) {
            throw new QueryResolutionException;
        }

        return call_user_func_array($this->queryResolver, [$request]);
    }

    /**
     * Set the query resolver callback.
     */
    public function withQuery(Closure $callback): static
    {
        $this->queryResolver = $callback;

        return $this;
    }

    /**
     * Handle the callback for the field resolution.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        $field->setAttribute('form', $this->getKey());
        $field->id($this->getKey().'-'.$field->getAttribute('id'));
        $field->resolveErrorsUsing(function (Request $request): MessageBag {
            return $this->errors($request);
        });

        if ($field instanceof Relation) {
            $field->resolveRouteKeyNameUsing(function () use ($field): string {
                return Str::of($field->getRelationName())->singular()->ucfirst()->prepend($this->getKey())->value();
            });
        }
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
     * Set the standalone property.
     */
    public function standalone(bool $value = true): static
    {
        $this->standalone = $value;

        return $this;
    }

    /**
     * Determine if the action is standalone.
     */
    public function isStandalone(): bool
    {
        return $this->standalone;
    }

    /**
     * Handle the request.
     */
    public function handleFormRequest(Request $request, Model $model): void
    {
        $this->validateFormRequest($request, $model);

        $models = match (true) {
            $this->isStandalone() => new Collection([$model]),
            $request->boolean('all') => $this->resolveQuery($request)->get(),
            default => $this->resolveQuery($request)->findMany($request->input('models', [])),
        };

        $this->handle($request, $models);

        if (in_array(HasRootEvents::class, class_uses_recursive($model))) {
            $models->each(static function (Model $model) use ($request): void {
                $model->recordRootEvent(
                    Str::of(static::class)->classBasename()->headline()->value(),
                    $request->user()
                );
            });
        }
    }

    /**
     * Perform the action.
     */
    public function perform(Request $request): Response
    {
        try {
            DB::beginTransaction();

            $this->handleFormRequest($request, $this->resolveQuery($request)->getModel());

            DB::commit();

            return Redirect::back()->with(
                sprintf('alerts.action-%s', $this->getKey()),
                Alert::info(__(':action was successful!', ['action' => $this->getName()]))
            );
        } catch (Throwable $exception) {
            report($exception);

            DB::rollBack();

            throw new SaveFormDataException($exception->getMessage());
        }
    }

    /**
     * Register the routes using the given router.
     */
    public function registerRoutes(Request $request, Router $router): void
    {
        $this->__registerRoutes($request, $router);

        $router->prefix($this->getUriKey())->group(function (Router $router) use ($request): void {
            $this->resolveFields($request)->registerRoutes($request, $router);
        });
    }

    /**
     * Get the route middleware for the registered routes.
     */
    public function getRouteMiddleware(): array
    {
        return [
            Authorize::class.':action',
        ];
    }

    /**
     * The routes that should be registered.
     */
    public function routes(Router $router): void
    {
        $router->post('/', ActionController::class);
    }

    /**
     * Convert the element to a JSON serializable format.
     */
    public function jsonSerialize(): mixed
    {
        return json_encode($this->toArray());
    }

    /**
     * Convert the action to an array.
     */
    public function toArray(): array
    {
        return [
            'confirmable' => $this->isConfirmable(),
            'destructive' => $this->isDestructive(),
            'key' => $this->getKey(),
            'modalKey' => $this->getModalKey(),
            'name' => $this->getName(),
            'template' => $this->template,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function toForm(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'url' => ! is_null($request->route()) ? $this->replaceRoutePlaceholders($request->route()) : null,
            'open' => $this->errors($request)->isNotEmpty(),
            'fields' => $this->resolveFields($request)->mapToInputs($request, $model),
        ]);
    }
}
