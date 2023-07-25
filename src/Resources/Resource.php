<?php

namespace Cone\Root\Resources;

use Cone\Root\Extracts\Extract;
use Cone\Root\Form\Form;
use Cone\Root\Http\Controllers\ResourceController;
use Cone\Root\Interfaces\Routable;
use Cone\Root\Navigation\Item as NavigationItem;
use Cone\Root\Root;
use Cone\Root\Support\Facades\Navigation;
use Cone\Root\Table\Table;
use Cone\Root\Traits\AsForm;
use Cone\Root\Traits\AsTable;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\RegistersRoutes;
use Cone\Root\Traits\ResolvesExtracts;
use Cone\Root\Traits\ResolvesRelations;
use Cone\Root\Traits\ResolvesWidgets;
use Cone\Root\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class Resource implements Routable
{
    use AsForm;
    use AsTable;
    use Authorizable;
    use ResolvesExtracts;
    use ResolvesRelations;
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
    protected string $icon = 'archive';

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
        return Str::of($this->getKey())->singular()->replace('-', '_')->prepend('resource_')->value();
    }

    /**
     * Get the URI of the resource.
     */
    public function getUri(): string
    {
        return Str::start(sprintf('%s/%s', App::make(Root::class)->getPath(), $this->getKey()), '/');
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
     * Get the policy for the model.
     */
    public function getPolicy(): mixed
    {
        return Gate::getPolicyFor($this->getModel());
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
        $extract->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        })->query(function () use ($request): Builder {
            return $this->resolveQuery($request);
        });
    }

    /**
     * Handle the created event.
     */
    public function created(Request $request, Model $model): void
    {
        //
    }

    /**
     * Handle the updated event.
     */
    public function updated(Request $request, Model $model): void
    {
        //
    }

    /**
     * Handle the deleted event.
     */
    public function deleted(Request $request, Model $model): void
    {
        //
    }

    /**
     * Handle the restored event.
     */
    public function restored(Request $request, Model $model): void
    {
        //
    }

    /**
     * Determine if the model soft deletable.
     */
    public function isSoftDeletable(): bool
    {
        return in_array(SoftDeletes::class, class_uses_recursive($this->getModel()));
    }

    /**
     * Get the table instance for the resource.
     */
    public function toTable(Request $request): Table
    {
        if (is_null($this->table)) {
            $this->table = Table::make()->query(function () use ($request): Builder {
                return $this->resolveQuery($request);
            });
        }

        return $this->table;
    }

    /**
     * Get the form instance for the resource.
     */
    public function toForm(Request $request): Form
    {
        if (is_null($this->form)) {
            $this->form = Form::make()->model(function () use ($request): Model {
                return $request->route($this->getRouteKeyName(), $this->getModelInstance());
            });
        }

        return $this->form;
    }

    /**
     * Get the index representation of the resource.
     */
    public function toIndex(Request $request): array
    {
        return [
            'resource' => $this,
            'title' => $this->getName(),
            'table' => $this->toTable($request),
            'widgets' => $this->resolveWidgets($request)->authorized($request),
        ];
    }

    /**
     * Get the create representation of the resource.
     */
    public function toCreate(Request $request): array
    {
        return [
            'resource' => $this,
            'form' => $this->toForm($request),
            'title' => __('Create :model', ['model' => $this->getModelName()]),
        ];
    }

    /**
     * Get the show representation of the resource.
     */
    public function toShow(Request $request, Model $model): array
    {
        return [
            // 'actions' => $this->resolveActions($request),
            'resource' => $this,
            'form' => $this->toForm($request)->model(fn (): Model => $model),
            'title' => __(':model: :id', ['model' => $this->getModelName(), 'id' => $model->getKey()]),
            // 'widgets' => $this->resolveWidgets($request)->authorized($request)->toArray(),
            // 'relations' => $this->resolveRelations($request)
            //     ->authorized($request, $model)
            //     ->mapToTable($request, $model),
        ];
    }

    /**
     * Get the navigation compatible format of the resource.
     */
    public function toNavigationItem(Request $request): NavigationItem
    {
        return (new NavigationItem($this->getUri(), $this->getName()))->icon($this->icon);
    }

    /**
     * Handle the resource registered event.
     */
    public function boot(Root $root): void
    {
        $root->routes(function (Router $router): void {
            $this->registerRoutes($router);
        });

        Navigation::location('sidebar')->add($this->toNavigationItem($root->app['request']));
    }

    /**
     * Register the routes.
     */
    public function registerRoutes(Router $router): void
    {
        $router->group(['__resource__' => $this->getKey()], function (Router $router) {
            $this->__registerRoutes($router);

            $request = App::make('request');

            $router->prefix($this->getUriKey())->group(function (Router $router) use ($request): void {
                $this->resolveWidgets($request)->registerRoutes($router);
                $this->resolveExtracts($request)->registerRoutes($router);
                $this->toTable($request)->registerRoutes($router);

                $router->prefix("{{$this->getRouteKeyName()}}")->group(function (Router $router) use ($request): void {
                    $this->toForm($request)->registerRoutes($router);
                    $this->resolveRelations($request)->registerRoutes($router);
                });
            });
        });
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
     * The routes that should be registered.
     */
    public function routes(Router $router): void
    {
        $router->get('/', [ResourceController::class, 'index']);
        $router->get('/create', [ResourceController::class, 'create']);
        $router->post('/', [ResourceController::class, 'store']);
        $router->get("{{$this->getRouteKeyName()}}", [ResourceController::class, 'edit']);
        $router->patch("{{$this->getRouteKeyName()}}", [ResourceController::class, 'update']);
        $router->delete("{{$this->getRouteKeyName()}}", [ResourceController::class, 'destroy']);

        if ($this->isSoftDeletable()) {
            $router->post("{{$this->getRouteKeyName()}}/restore", [ResourceController::class, 'restore']);
        }
    }

    /**
     * Handle the routes registered event.
     */
    public function routesRegistered(Router $router): void
    {
        //
    }
}
