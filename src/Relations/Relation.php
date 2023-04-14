<?php

namespace Cone\Root\Relations;

use Cone\Root\Fields\Field;
use Cone\Root\Http\Controllers\RelationController;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesActions;
use Cone\Root\Traits\ResolvesFields;
use Cone\Root\Traits\ResolvesFilters;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

abstract class Relation implements Arrayable, Routable
{
    use Authorizable;
    use Makeable;
    use ResolvesActions;
    use ResolvesFields;
    use ResolvesFilters;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
        RegistersRoutes::onRouteMatched as __onRouteMatched;
    }

    /**
     * The relation name on the model.
     */
    protected string $relation;

    /**
     * The relation label.
     */
    protected string $label;

    /**
     * Create a new relation instance.
     */
    public function __construct(string $label, string $relation)
    {
        $this->label = $label;
        $this->relation = $relation;
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return $this->relation;
    }

    /**
     * Get the route key name.
     */
    public function getRouteKeyName(): string
    {
        return Str::of($this->relation)->singular()->prepend('relation_')->value();
    }

    /**
     * Get the relation instance.
     */
    public function getRelation(Model $model): EloquentRelation
    {
        return call_user_func([$model, $this->relation]);
    }

    /**
     * Handle the resolving event on the field instance.
     */
    protected function resolveField(Request $request, Field $field): void
    {
        $field->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        });
    }

    /**
     * Register the routes using the given router.
     */
    public function registerRoutes(Router $router): void
    {
        $this->__registerRoutes($router);

        $request = App::make('request');

        $router->prefix($this->getUriKey())->group(function (Router $router) use ($request): void {
            $this->resolveFields($request)->registerRoutes($router);
        });
    }

    /**
     * The routes that should be registered.
     */
    public function routes(Router $router): void
    {
        $router->get('/', [RelationController::class, 'index']);
        $router->post('/', [RelationController::class, 'store']);
        $router->get('/create', [RelationController::class, 'create']);
        $router->get("/{{$this->getRouteKeyName()}}", [RelationController::class, 'show']);
        $router->get("/{{$this->getRouteKeyName()}}/edit", [RelationController::class, 'edit']);
        $router->patch("/{{$this->getRouteKeyName()}}", [RelationController::class, 'update']);
        $router->delete("/{{$this->getRouteKeyName()}}", [RelationController::class, 'destroy']);
    }

    /**
     * Register the route constraints.
     */
    public function registerRouteConstraints(Router $router): void
    {
        $router->bind($this->getRouteKeyName(), function (string $id, Route $route): Model {
            $relation = $this->getRelation($route->parentOfParameter($this->getRouteKeyName()));

            if ($relation instanceof BelongsToMany) {
                return $id === 'create'
                    ? $relation->newPivot()
                    : $relation->findOrFail($id)->getRelation($relation->getPivotAccessor());
            }

            return $id === 'create'
                ? $relation->getRelated()
                : $relation->findOrFail($id);
        });

        $router->pattern(
            $this->getRouteKeyName(),
            '[0-9]+|[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}|create'
        );
    }

    /**
     * Replace the route placeholders with the route parameters.
     */
    protected function replaceRoutePlaceholders(Request $request): string
    {
        $uri = $this->getUri();

        foreach ($request->route()->originalParameters() as $key => $value) {
            $uri = str_replace("{{$key}}", $value, $uri);
        }

        return preg_replace('/\{.*?\}/', 'create', $uri);
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
        ];
    }

    /**
     * Get the index representation of the relation.
     */
    public function toIndex(Request $request): array
    {
        return array_merge($this->toArray(), [
            'url' => $this->replaceRoutePlaceholders($request),
        ]);
    }
}
