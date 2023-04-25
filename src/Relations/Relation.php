<?php

namespace Cone\Root\Relations;

use Cone\Root\Enums\ResourceContext;
use Cone\Root\Fields\Field;
use Cone\Root\Http\Controllers\RelationController;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Root;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\Makeable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesActions;
use Cone\Root\Traits\ResolvesFields;
use Cone\Root\Traits\ResolvesFilters;
use Cone\Root\Traits\ResolvesWidgets;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

abstract class Relation implements Arrayable, Routable
{
    use Authorizable;
    use Makeable;
    use ResolvesActions;
    use ResolvesFields;
    use ResolvesFilters;
    use ResolvesWidgets;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
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
     * Make a new related instance.
     */
    public function newRelated(Model $model): Model
    {
        $relation = $this->getRelation($model);

        return $relation->getQuery()->newModelInstance()->setRelation('parent', $model);
    }

    /**
     * Get the relation abilities.
     */
    public function getAbilities(Model $model): array
    {
        $name = Str::of($this->relation)->singular()->ucfirst()->value();

        $policy = Gate::getPolicyFor($model);

        return [
            'viewAny' => is_null($policy) || Gate::allows('viewAny'.$name, $model),
            'create' => is_null($policy) || Gate::allows('add'.$name, $model),
        ];
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
     * Map the related models.
     */
    public function mapItems(Request $request, Model $model): array
    {
        $filters = $this->resolveFilters($request)->authorized($request);

        $relation = $this->getRelation($model);

        $query = $filters->apply($request, $relation->getQuery())->latest();

        $items = $relation->paginate($request->input('per_page'))
            ->withQueryString()
            ->setPath($this->replaceRoutePlaceholders($request->route()))
            ->through(function (Model $related) use ($request, $model): array {
                return $this->newItem($model, $related)->toDisplay(
                    $request, $this->resolveFields($request)->authorized($request, $related)
                );
            })
            ->toArray();

        return array_merge($items, [
            'query' => $filters->mapToQuery($request, $query),
        ]);
    }

    /**
     * Make a new item instance.
     */
    public function newItem(Model $model, Model $related): Item
    {
        $related->setRelation('parent', $model);

        return (new Item($related, $this->relation))->url(function (Request $request) use ($related): string {
            return $related->exists
                ? sprintf('%s/%s', $this->replaceRoutePlaceholders($request->route()), $related->getRouteKey())
                : $this->replaceRoutePlaceholders($request->route());
        });
    }

    /**
     * Resolve the resource model for a bound value.
     */
    public function resolveRouteBinding(Request $request, string $id): Model
    {
        return $this->getRelation($request->route()->parentOfParameter($this->getRouteKeyName()))->findOrFail($id);
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
            $this->resolveActions($request)->registerRoutes($router);
            $this->resolveWidgets($request)->registerRoutes($router);
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
        $router->bind($this->getRouteKeyName(), function (string $id) use ($router): Model {
            return $this->resolveRouteBinding($router->getCurrentRequest(), $id);
        });
    }

    /**
     * Handle the routes registered event.
     */
    public function routesRegistered(Router $router): void
    {
        App::make(Root::class)->breadcrumbs->patterns([
            $this->getUri() => $this->label,
            sprintf('%s/create', $this->getUri()) => __('Create'),
            sprintf('%s/{%s}', $this->getUri(), $this->getRouteKeyName()) => function (Request $request): string {
                return $request->route()->originalParameter($this->getRouteKeyName());
            },
            sprintf('%s/{%s}/edit', $this->getUri(), $this->getRouteKeyName()) => __('Edit'),
        ]);
    }

    /**
     * Get the related model name.
     */
    public function getRelatedName(): string
    {
        return __(Str::of($this->relation)->singular()->headline()->value());
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return [
            'label' => $this->label,
            'relatedName' => $this->getRelatedName(),
        ];
    }

    /**
     * Get the table representation of the relation.
     */
    public function toTable(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'url' => $this->replaceRoutePlaceholders($request->route()),
            'abilities' => $this->getAbilities($model),
            'items' => $this->getRelation($model)
                ->latest()
                ->paginate(5)
                ->through(function (Model $related) use ($request, $model): array {
                    return $this->newItem($model, $related)->toDisplay(
                        $request, $this->resolveFields($request)->authorized($request, $related)
                    );
                })
                ->toArray(),
        ]);
    }

    /**
     * Get the index representation of the relation.
     */
    public function toIndex(Request $request, Model $model): array
    {
        $relation = $this->getRelation($model);

        return [
            'actions' => $this->resolveActions($request)
                ->authorized($request)
                ->visible(ResourceContext::Index->value)
                ->mapToForm($request, $relation->getRelated()),
            'filters' => $this->resolveFilters($request)
                ->authorized($request)
                ->mapToForm($request),
            'items' => $this->mapItems($request, $model),
            'title' => $this->label,
            'widgets' => $this->resolveWidgets($request)->authorized($request)->toArray(),
            'relation' => array_merge($this->toArray(), [
                'url' => $this->replaceRoutePlaceholders($request->route()),
                'abilities' => $this->getAbilities($model),
            ]),
        ];
    }

    /**
     * Get the index representation of the relation.
     */
    public function toCreate(Request $request, Model $model): array
    {
        $related = $this->newRelated($model);

        return [
            'model' => $this->newItem($model, $related)->toForm(
                $request,
                $this->resolveFields($request)->authorized($request, $model)->visible(ResourceContext::Create->value)
            ),
            'title' => __('Create :model', ['model' => $this->getRelatedName()]),
        ];
    }

    /**
     * Get the index representation of the relation.
     */
    public function toShow(Request $request, Model $model, Model $related): array
    {
        return [
            'model' => ($item = $this->newItem($model, $related))->toDisplay(
                $request,
                $this->resolveFields($request)->authorized($request, $model)->visible(ResourceContext::Show->value)
            ),
            'resource' => $request->route('rootResource')->toArray(),
            'title' => __(':model: :id', ['model' => $this->getRelatedName(), 'id' => $item->model->getKey()]),
        ];
    }

    /**
     * Get the index representation of the relation.
     */
    public function toEdit(Request $request, Model $model, Model $related): array
    {
        return [
            'model' => ($item = $this->newItem($model, $related))->toForm(
                $request,
                $this->resolveFields($request)->authorized($request, $model)->visible(ResourceContext::Update->value)
            ),
            'title' => __('Edit :model: :id', ['model' => $this->getRelatedName(), 'id' => $item->model->getKey()]),
        ];
    }
}
