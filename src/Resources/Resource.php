<?php

namespace Cone\Root\Resources;

use Cone\Root\Extracts\Extract;
use Cone\Root\Forms\Form;
use Cone\Root\Http\Controllers\ResourceController;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Root;
use Cone\Root\Tables\Table;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\MapsAbilities;
use Cone\Root\Traits\ResolvesExtracts;
use Cone\Root\Traits\ResolvesWidgets;
use Cone\Root\Widgets\Widget;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;

class Resource implements Arrayable
{
    use Authorizable;
    use MapsAbilities;
    use ResolvesExtracts;
    use ResolvesWidgets;

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
     * Get the route key name.
     */
    public function getRouteKeyName(): string
    {
        return Str::of($this->getKey())->singular()->prepend('resource_')->value();
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
    public function resolveQuery(ResourceRequest $request): Builder
    {
        return $this->query();
    }

    /**
     * Resolve the resource model for a bound value.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     */
    public function resolveRouteBinding(ResourceRequest $request, string $id): Model
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
    protected function resolveExtract(RootRequest $request, Extract $extract): void
    {
        $extract->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        })->withQuery(function (RootRequest $request): Builder {
            return $this->resolveQuery($request);
        });
    }

    /**
     * Handle the resolving event on the widget instance.
     */
    protected function resolveWidget(RootRequest $request, Widget $widget): void
    {
        $widget->mergeAuthorizationResolver(function (...$parameters): bool {
            return $this->authorized(...$parameters);
        });
    }

    /**
     * Map the URLs.
     */
    public function mapUrls(RootRequest $request): array
    {
        return [
            'index' => $this->getUri(),
            'create' => sprintf('%s/create', $this->getUri()),
        ];
    }

    /**
     * Get the mappable abilities.
     */
    public function getAbilities(): array
    {
        return ['viewAny', 'create'];
    }

    /**
     * Handle the created event.
     */
    public function created(CreateRequest $request, Model $model): void
    {
        //
    }

    /**
     * Handle the updated event.
     */
    public function updated(UpdateRequest $request, Model $model): void
    {
        //
    }

    /**
     * Handle the deleted event.
     */
    public function deleted(ResourceRequest $request, Model $model): void
    {
        //
    }

    /**
     * Handle the restored event.
     */
    public function restored(ResourceRequest $request, Model $model): void
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
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        $request = App::make(RootRequest::class);

        return [
            'abilities' => $this->mapAbilities($request, $this->getModelInstance()),
            'key' => $this->getKey(),
            'icon' => $this->getIcon(),
            'model_name' => $this->getModelName(),
            'name' => $this->getName(),
            'urls' => $this->mapUrls($request),
        ];
    }

    /**
     * Get the index representation of the resource.
     */
    public function toIndex(IndexRequest $request): array
    {
        return [
            'breadcrumbs' => [],
            'resource' => $this->toArray(),
            'title' => $this->getName(),
            'widgets' => $this->resolveWidgets($request)->available($request)->toArray(),
            'table' => $this->toTable($request)->build($request),
        ];
    }

    /**
     * Get the create representation of the resource.
     */
    public function toCreate(Request $request): array
    {
        return [
            'breadcrumbs' => [],
            'model' => $this->toForm($request)->build($request, $this->getModelInstance()),
            'resource' => $this->toArray(),
            'title' => __('Create :model', ['model' => $this->getModelName()]),
        ];
    }

    /**
     * Get the edit representation of the resource.
     */
    public function toEdit(UpdateRequest $request, Model $model): array
    {
        return [
            'breadcrumbs' => [],
            'model' => (new Item($model))->toForm(
                $request, $this->resolveFields($request)->available($request, $model)
            ),
            'resource' => $this->toArray(),
            'title' => __('Edit :model: :id', ['model' => $this->getModelName(), 'id' => $model->getKey()]),
        ];
    }

    /**
     * Handle the resource registered event.
     */
    public function boot(Root $root): void
    {
        $this->registerRoutes($root);

        $root->app->make('router')->bind($this->getRouteKeyName(), function (string $id) use ($root): Model {
            return $id === 'create'
                ? $this->getModelInstance()
                : $this->resolveRouteBinding($root->app->make(ResourceRequest::class), $id);
        });

        $root->app->make('router')->pattern(
            $this->getRouteKeyName(),
            '[0-9]+|[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}|create'
        );
    }

    /**
     * Register the routes for the resource.
     */
    protected function registerRoutes(Root $root): void
    {
        $root->routes(function (Router $router) use ($root): void {
            $router->group(
                ['prefix' => $this->getKey(), 'resource' => $this->getKey()],
                function (Router $router) use ($root): void {
                    if (! $root->app->routesAreCached()) {
                        $router->as(sprintf('%s.', $this->getKey()))->group(function (Router $router): void {
                            $this->routes($router);
                        });
                    }
                }
            );
        });
    }

    /**
     * The routes that should be registered.
     */
    public function routes(Router $router): void
    {
        $router->get('/', [ResourceController::class, 'index'])->name('index');
        $router->get('/create', [ResourceController::class, 'create'])->name('create');
        $router->post('/', [ResourceController::class, 'store'])->name('store');
        $router->get("{{$this->getRouteKeyName()}}", [ResourceController::class, 'show'])->name('show');
        $router->patch("{{$this->getRouteKeyName()}}", [ResourceController::class, 'update'])->name('update');
        $router->delete("{{$this->getRouteKeyName()}}", [ResourceController::class, 'destroy'])->name('destroy');

        if ($this->isSoftDeletable()) {
            $router->post("{{$this->getRouteKeyName()}}/restore", [ResourceController::class, 'restore'])->name('restore');
        }
    }

    /**
     * Get the table representation of the resource.
     */
    public function toTable(Request $request): Table
    {
        return (new Table($this->getModelInstance()))
                ->withQuery(fn (): Builder => $this->query());
    }

    /**
     * Get the table representation of the resource.
     */
    public function toForm(Request $request): Form
    {
        return new Form();
    }
}
