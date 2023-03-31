<?php

namespace Cone\Root\Resources;

use Cone\Root\Actions\Action;
use Cone\Root\Extracts\Extract;
use Cone\Root\Forms\Form;
use Cone\Root\Http\Controllers\ResourceController;
use Cone\Root\Interfaces\HasForm;
use Cone\Root\Interfaces\HasTable;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Root;
use Cone\Root\Tables\Table;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesActions;
use Cone\Root\Traits\ResolvesExtracts;
use Cone\Root\Traits\ResolvesFields;
use Cone\Root\Traits\ResolvesFilters;
use Cone\Root\Traits\ResolvesWidgets;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Resource implements Arrayable, HasForm, HasTable, Routable
{
    use ResolvesActions;
    use ResolvesExtracts;
    use ResolvesFields;
    use ResolvesFilters;
    use ResolvesWidgets;
    use RegistersRoutes {
        RegistersRoutes::registerRoutes as __registerRoutes;
    }

    /**
     * The model class.
     */
    protected string $model;

    /**
     * The relations to eager load on every query.
     */
    protected array $with = [];

    /**
     * The relations to eager load on every query.
     */
    protected array $withCount = [];

    /**
     * The icon for the resource.
     */
    protected string $icon = 'inventory-2';

    /**
     * Create a new resource instance.
     */
    public function __construct(string $model)
    {
        $this->model = $model;
    }

    /**
     * Get the model for the resource.
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Get the key.
     */
    public function getKey(): string
    {
        return Str::of($this->getModel())->classBasename()->plural()->kebab()->value();
    }

    /**
     * Get the URI key.
     */
    public function getUriKey(): string
    {
        return $this->getKey();
    }

    /**
     * Get the route key name.
     */
    public function getRouteKeyName(): string
    {
        return Str::of($this->getKey())->singular()->prepend('resource_')->value();
    }

    /**
     * Get the name.
     */
    public function getName(): string
    {
        return __(Str::of($this->getModel())->classBasename()->headline()->plural()->value());
    }

    /**
     * Get the model name.
     */
    public function getModelName(): string
    {
        return __(Str::of($this->getModel())->classBasename()->value());
    }

    /**
     * Get the model instance.
     */
    public function getModelInstance(): Model
    {
        return new ($this->getModel());
    }

    /**
     * Set the resource icon.
     */
    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Get the resource icon.
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * Set the relations to eagerload.
     */
    public function with(array $relations): static
    {
        $this->with = $relations;

        return $this;
    }

    /**
     * Set the relation counts to eagerload.
     */
    public function withCount(array $relations): static
    {
        $this->withCount = $relations;

        return $this;
    }

    /**
     * Make a new Eloquent query instance.
     */
    public function query(): Builder
    {
        return $this->getModelInstance()->newQuery()->with($this->with)->withCount($this->withCount);
    }

    /**
     * Resolve the query for the given request.
     */
    public function resolveQuery(Request $request): Builder
    {
        return $this->query();
    }

    /**
     * Resolve the resource model for a bound value.
     */
    public function resolveRouteBinding(Request $request, string $id): Model
    {
        return $this->resolveQuery($request)
                    ->when($this->isSoftDeletable(), static function (Builder $query): Builder {
                        return $query->withTrashed();
                    })
                    ->findOrFail($id);
    }

    /**
     * Handle the resolving event on the extract instance.
     */
    protected function resolveExtract(Request $request, Extract $extract): void
    {
        $extract->withQuery(function (Request $request): Builder {
            return $this->resolveQuery($request);
        });
    }

    /**
     * Handle the resolving event on the action instance.
     */
    protected function resolveAction(Request $request, Action $action): void
    {
        $action->withQuery(function (Request $request): Builder {
            return $this->resolveFilters($request)
                        ->available($request)
                        ->apply($request, $this->resolveQuery($request));
        });
    }

    /**
     * Determine if the model soft deletable.
     */
    public function isSoftDeletable(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this->getModel()));
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return [
            'abilities' => (new Item($this->getModelInstance()))->mapAbilities(App::make('request')),
            'key' => $this->getKey(),
            'icon' => $this->getIcon(),
            'model_name' => $this->getModelName(),
            'name' => $this->getName(),
            'url' => $this->getUri(),
        ];
    }

    /**
     * Get the index representation of the resource.
     */
    public function toIndex(Request $request): array
    {
        return array_merge($this->toArray(), [
            'title' => $this->getName(),
            'widgets' => $this->resolveWidgets($request)->toArray(),
            'table' => $this->toTable($request)->build($request),
        ]);
    }

    /**
     * Get the create representation of the resource.
     */
    public function toCreate(Request $request): array
    {
        return array_merge($this->toArray(), [
            'form' => $this->toForm($request, $this->getModelInstance())->build($request),
            'title' => __('Create :model', ['model' => $this->getModelName()]),
        ]);
    }

    /**
     * Get the edit representation of the resource.
     */
    public function toEdit(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'form' => $this->toForm($request, $model)->build($request),
            'title' => __('Edit :model: :id', ['model' => $this->getModelName(), 'id' => $model->getKey()]),
            'labels' => [
                'save' => __('Save'),
                'restore' => __('Restore'),
                'delete' => __('Delete'),
                'force_delete' => __('Delete permanently'),
                'model_name' => $this->getModelName(),
            ],
        ]);
    }

    /**
     * Get the edit representation of the resource.
     */
    public function toShow(Request $request, Model $model): array
    {
        return array_merge($this->toArray(), [
            'actions' => $this->resolveActions($request)->available($request)->mapToForm($request, $model)->toArray(),
            'model' => (new Item($model))->toDisplay($request, $this->resolveFields($request)),
            'relations' => [],
            'title' => __(':model: :id', ['model' => $this->getModelName(), 'id' => $model->getKey()]),
        ]);
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
     * Convert the resource to a table.
     */
    public function toTable(Request $request): Table
    {
        $query = $this->resolveQuery($request);

        return new Table(
            $query,
            $this->resolveFields($request)->authorized($request, $query->getModel()),
            $this->resolveActions($request),
            $this->resolveFilters($request)
        );
    }

    /**
     * Handle the resource registered event.
     */
    public function boot(Root $root): void
    {
        $this->resolveFields($root->app['request']);
        $this->resolveFilters($root->app['request']);
        $this->resolveActions($root->app['request']);
        $this->resolveWidgets($root->app['request']);
        $this->resolveExtracts($root->app['request']);

        $root->routes(function (Router $router): void {
            $this->registerRoutes($router);
        });
    }

    /**
     * Register the routes.
     */
    public function registerRoutes(Router $router): void
    {
        $this->__registerRoutes($router);

        $router->prefix($this->getUriKey())->group(function (Router $router): void {
            $this->widgets->registerRoutes($router);
            $this->extracts->registerRoutes($router);
            $this->actions->registerRoutes($router);
            $router->prefix("{{$this->getRouteKeyName()}}")->group(function (Router $router): void {
                $this->fields->registerRoutes($router);
            });
        });
    }

    /**
     * Register the route constraints.
     */
    public function registerRouteConstraints(Router $router): void
    {
        $router->bind($this->getRouteKeyName(), function (string $id) use ($router): Model {
            return $id === 'create'
                ? $this->getModelInstance()
                : $this->resolveRouteBinding($router->getCurrentRequest(), $id);
        });
    }

    /**
     * The routes that should be registered.
     */
    public function routes(Router $router): void
    {
        $router->get('/', [ResourceController::class, 'index']);
        $router->get('/create', [ResourceController::class, 'create']);
        $router->post('/', [ResourceController::class, 'store']);
        $router->get("{{$this->getRouteKeyName()}}", [ResourceController::class, 'show']);
        $router->get("{{$this->getRouteKeyName()}}/edit", [ResourceController::class, 'edit']);
        $router->patch("{{$this->getRouteKeyName()}}", [ResourceController::class, 'update']);
        $router->delete("{{$this->getRouteKeyName()}}", [ResourceController::class, 'destroy']);

        if ($this->isSoftDeletable()) {
            $router->post("{{$this->getRouteKeyName()}}/restore", [ResourceController::class, 'restore']);
        }
    }
}
