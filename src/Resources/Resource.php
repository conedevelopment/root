<?php

namespace Cone\Root\Resources;

use Closure;
use Cone\Root\Fields\Field;
use Cone\Root\Interfaces\Registries\Item;
use Cone\Root\Support\Collections\Actions;
use Cone\Root\Support\Collections\Extracts;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Support\Collections\Filters;
use Cone\Root\Support\Collections\Widgets;
use Cone\Root\Widgets\Widget;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class Resource implements Arrayable, Item
{
    public const INDEX = 'index';
    public const SHOW = 'show';
    public const UPDATE = 'update';
    public const CREATE = 'create';

    /**
     * The model class.
     *
     * @var string
     */
    protected string $model;

    /**
     * The relations to eager load on every query.
     *
     * @var array
     */
    protected array $with = [];

    /**
     * The fields resolver.
     *
     * @var \Closure|null
     */
    protected ?Closure $fieldsResolver = null;

    /**
     * The filters resolver.
     *
     * @var \Closure|null
     */
    protected ?Closure $filtersResolver = null;

    /**
     * The actions resolver.
     *
     * @var \Closure|null
     */
    protected ?Closure $actionsResolver = null;

    /**
     * The extracts resolver.
     *
     * @var \Closure|null
     */
    protected ?Closure $extractsResolver = null;

    /**
     * The widgets resolver.
     *
     * @var \Closure|null
     */
    protected ?Closure $widgetsResolver = null;

    /**
     * Create a new resource instance.
     *
     * @param  string  $model
     * @return void
     */
    public function __construct(string $model)
    {
        $this->model = $model;
    }

    /**
     * Get the model for the resource.
     *
     * @return string
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Get the key.
     *
     * @return string
     */
    public function getKey(): string
    {
        return Str::of($this->getModel())->classBasename()->plural()->kebab();
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return Str::of($this->getModel())->classBasename()->headline()->plural();
    }

    /**
     * Get the model name.
     *
     * @return string
     */
    public function getModelName(): string
    {
        return Str::of($this->getModel())->classBasename();
    }

    /**
     * Get the model instance.
     *
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function getModelInstance(): Model
    {
        static $instance;

        if (! isset($instance)) {
            $instance = new ($this->getModel());
        }

        return $instance;
    }

    /**
     * Retrieve the model for a bound value.
     *
     * @param  string  $id
     * @return \Illuminate\Database\Eloquent\Model|null
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function resolveRouteBinding(string $id): ?Model
    {
        $model = $this->getModelInstance()->resolveRouteBinding($id);

        if (is_null($model)) {
            throw (new ModelNotFoundException())->setModel($this->getModel(), $id);
        }

        return $model;
    }

    /**
     * Set the relations to eagerload.
     *
     * @param  array  $relations
     * @return $this
     */
    public function with(array $relations): static
    {
        $this->with = $relations;

        return $this;
    }

    /**
     * Make a new eloquent query instance.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function query(): Builder
    {
        return $this->getModelInstance()->newQuery()->with($this->with);
    }

    /**
     * Apply the filters on a new eloquent query instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Cone\Root\Support\Collections\Filters  $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function filteredQuery(Request $request, Filters $filters): Builder
    {
        return $filters->apply($this->query(), $request);
    }

    /**
     * Define the fields for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [];
    }

    /**
     * Set the fields resolver.
     *
     * @param  array|\Closure  $fields
     * @return $this
     */
    public function withFields(array|Closure $fields): static
    {
        if (is_array($fields)) {
            $fields = static function (Request $request, Fields $collection) use ($fields): Fields {
                return $collection->merge($fields);
            };
        }

        $this->fieldsResolver = $fields;

        return $this;
    }

    /**
     * Resolve fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Fields
     */
    public function resolveFields(Request $request): Fields
    {
        $fields = Fields::make($this->fields($request));

        if (! is_null($this->fieldsResolver)) {
            return call_user_func_array($this->fieldsResolver, [$request, $fields]);
        }

        return $fields;
    }

    /**
     * Define the filters for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function filters(Request $request): array
    {
        return [];
    }

    /**
     * Set the filters resolver.
     *
     * @param  array|\Closure  $filters
     * @return $this
     */
    public function withFilters(array|Closure $filters): static
    {
        if (is_array($filters)) {
            $filters = static function (Request $request, Filters $collection) use ($filters): Filters {
                return $collection->merge($filters);
            };
        }

        $this->filtersResolver = $filters;

        return $this;
    }

    /**
     * Resolve filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Filters
     */
    public function resolveFilters(Request $request): Filters
    {
        $filters = Filters::make($this->filters($request));

        if (! is_null($this->filtersResolver)) {
            return call_user_func_array($this->filtersResolver, [$request, $filters]);
        }

        return $filters;
    }

    /**
     * Define the actions for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function actions(Request $request): array
    {
        return [];
    }

    /**
     * Set the actions resolver.
     *
     * @param  array|\Closure  $actions
     * @return $this
     */
    public function withActions(array|Closure $actions): static
    {
        if (is_array($actions)) {
            $actions = static function (Request $request, Actions $collection) use ($actions): Actions {
                return $collection->merge($actions);
            };
        }

        $this->actionsResolver = $actions;

        return $this;
    }

    /**
     * Resolve the actions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Actions
     */
    public function resolveActions(Request $request): Actions
    {
        $actions = Actions::make($this->actions($request));

        if (! is_null($this->actionsResolver)) {
            return call_user_func_array($this->actionsResolver, [$request, $actions]);
        }

        return $actions;
    }

    /**
     * Define the extracts for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function extracts(Request $request): array
    {
        return [];
    }

    /**
     * Set the extracts resolver.
     *
     * @param  array|\Closure  $extracts
     * @return $this
     */
    public function withExtracts(array|Closure $extracts): static
    {
        if (is_array($extracts)) {
            $extracts = static function (Request $request, Extracts $collection) use ($extracts): Extracts {
                return $collection->merge($extracts);
            };
        }

        $this->extractsResolver = $extracts;

        return $this;
    }

    /**
     * Resolve the extracts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Extracts
     */
    public function resolveExtracts(Request $request): Extracts
    {
        $extracts = Extracts::make($this->extracts($request));

        if (! is_null($this->extractsResolver)) {
            return call_user_func_array($this->extractsResolver, [$request, $extracts]);
        }

        return $extracts;
    }

    /**
     * Define the widgets for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function widgets(Request $request): array
    {
        return [];
    }

    /**
     * Set the widgets resolver.
     *
     * @param  array|\Closure  $widgets
     * @return $this
     */
    public function withWidgets(array|Closure $widgets): static
    {
        if (is_array($widgets)) {
            $widgets = static function (Request $request, Widgets $collection) use ($widgets): Widgets {
                return $collection->merge($widgets);
            };
        }

        $this->widgetsResolver = $widgets;

        return $this;
    }

    /**
     * Resolve the widgets.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Widgets
     */
    public function resolveWidgets(Request $request): Widgets
    {
        $widgets = Widgets::make($this->widgets($request));

        if (! is_null($this->widgetsResolver)) {
            return call_user_func_array($this->widgetsResolver, [$request, $widgets]);
        }

        return $widgets;
    }

    /**
     * Map the URLs.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function mapUrls(Request $request): array
    {
        return [
            'action' => URL::route('root.resource.action', $this->getKey()),
            'create' => URL::route('root.resource.create', $this->getKey()),
            'index' => URL::route('root.resource.index', $this->getKey()),
        ];
    }

    /**
     * Get the abilities.
     *
     * @return array
     */
    public function getAbilities(): array
    {
        return [
            'global' => ['viewAny', 'create'],
            'scoped' => ['view', 'update', 'delete', 'restore', 'forceDelete'],
        ];
    }

    /**
     * Get the policy.
     *
     * @return mixed
     */
    public function getPolicy(): mixed
    {
        return Gate::getPolicyFor($this->getModel());
    }

    /**
     * Map the abilities.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function mapAbilities(Request $request): array
    {
        $policy = $this->getPolicy();

        $abilities = $this->getAbilities();

        return array_reduce($abilities['global'], function (array $stack, $ability) use ($request, $policy): array {
            return array_merge($stack, [
                $ability => is_null($policy) || $request->user()?->can($ability, $this->getModel()),
            ]);
        }, []);
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
            'model_name' => $this->getModelName(),
            'urls' => App::call([$this, 'mapUrls']),
            'abilities' => App::call([$this, 'mapAbilities']),
        ];
    }

    /**
     * Get the index representation of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toIndex(Request $request): array
    {
        $filters = $this->resolveFilters($request);

        $fields = $this->resolveFields($request)->filterVisible($request, static::INDEX);

        $query = $this->filteredQuery($request, $filters)
                    ->latest()
                    ->paginate($request->input('per_page'))
                    ->withQueryString()
                    ->through(function (Model $model) use ($request, $fields): array {
                        return $model->toResourceDisplay($request, $this, $fields);
                    });

        return array_merge($this->toArray(), [
            'actions' => $this->resolveActions($request)->filterVisible($request, static::INDEX)->toArray(),
            'extracts' => $this->resolveExtracts($request)->toArray(),
            'filters' => $filters->toArray(),
            'query' => $query->toArray(),
            'widgets' => $this->resolveWidgets($request)->filterVisible($request, static::INDEX)->toArray(),
        ]);
    }

    /**
     * Get the index response of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function toIndexResponse(Request $request): Response
    {
        if ($this->getPolicy() && $request->user()->cannot('viewAny', $this->getModel())) {
            abort(RedirectResponse::HTTP_FORBIDDEN);
        }

        return Inertia::render('Resource/Index', $this->toIndex($request));
    }

    /**
     * Get the create representation of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toCreate(Request $request): array
    {
        $fields = $this->resolveFields($request)->filterVisible($request, static::CREATE);

        return array_merge($this->toArray(), [
            'model' => $this->getModelInstance()->newInstance()->toResourceForm($request, $this, $fields),
        ]);
    }

    /**
     * Get the create response of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Inertia\Response
     */
    public function toCreateResponse(Request $request): Response
    {
        if ($this->getPolicy() && $request->user()->cannot('create', $this->getModel())) {
            abort(RedirectResponse::HTTP_FORBIDDEN);
        }

        return Inertia::render('Resource/Create', $this->toCreate($request));
    }

    /**
     * Get the show representation of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toShow(Request $request, Model $model): array
    {
        $fields = $this->resolveFields($request)->filterVisible($request, static::SHOW);

        return array_merge($this->toArray(), [
            'actions' => $this->resolveActions($request)->filterVisible($request, static::SHOW)->toArray(),
            'model' => $model->toResourceDisplay($request, $this, $fields),
        ]);
    }

    /**
     * Get the show response of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Inertia\Response
     */
    public function toShowResponse(Request $request, string $id): Response
    {
        $model = $this->resolveRouteBinding($id);

        if ($this->getPolicy() && $request->user()->cannot('view', $model)) {
            abort(RedirectResponse::HTTP_FORBIDDEN);
        }

        return Inertia::render('Resource/Show', $this->toShow($request, $model));
    }

    /**
     * Get the edit representation of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toEdit(Request $request, Model $model): array
    {
        $fields = $this->resolveFields($request)->filterVisible($request, static::UPDATE);

        return array_merge($this->toArray(), [
            'model' => $model->toResourceForm($request, $this, $fields),
        ]);
    }

    /**
     * Get the edit response of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Inertia\Response
     */
    public function toEditResponse(Request $request, string $id): Response
    {
        $model = $this->resolveRouteBinding($id);

        if ($this->getPolicy() && $request->user()->cannot('update', $model)) {
            abort(RedirectResponse::HTTP_FORBIDDEN);
        }

        return Inertia::render('Resource/Edit', $this->toEdit($request, $model));
    }

    /**
     * Handle the store request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function handleStore(Request $request): Model
    {
        $fields = $this->resolveFields($request)->filterVisible($request, static::CREATE);

        $model = $this->getModelInstance()->newInstance();

        $request->validate(
            $fields->mapToValidate($request, $model, static::CREATE)->toArray()
        );

        $fields->each(static function (Field $field) use ($request, $model): void {
            $field->hydrate($request, $model, $request->input($field->name));
        });

        $model->save();

        return $model;
    }

    /**
     * Get the store response of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toStoreResponse(Request $request): RedirectResponse
    {
        if ($this->getPolicy() && $request->user()->cannot('create', $this->getModel())) {
            abort(RedirectResponse::HTTP_FORBIDDEN);
        }

        $model = $this->handleStore($request);

        return Redirect::route('root.resource.show', [$this->getKey(), $model]);
    }

    /**
     * Handle the update request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function handleUpdate(Request $request, Model $model): Model
    {
        $fields = $this->resolveFields($request)->filterVisible($request, static::UPDATE);

        $request->validate(
            $fields->mapToValidate($request, $model, static::UPDATE)->toArray()
        );

        $fields->each(static function (Field $field) use ($request, $model): void {
            $field->hydrate($request, $model, $request->input($field->name));
        });

        $model->save();

        return $model;
    }

    /**
     * Get the update response of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toUpdateResponse(Request $request, string $id): RedirectResponse
    {
        $model = $this->resolveRouteBinding($id);

        if ($this->getPolicy() && $request->user()->cannot('update', $model)) {
            abort(RedirectResponse::HTTP_FORBIDDEN);
        }

        $this->handleUpdate($request, $model);

        return Redirect::route('root.resource.show', [$this->getKey(), $model]);
    }

    /**
     * Handle the destroy request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function handleDestroy(Request $request, Model $model): Model
    {
        if (class_uses_recursive(SoftDeletes::class) && $model->trashed()) {
            $model->forceDelete();
        } else {
            $model->delete();
        }

        return $model;
    }

    /**
     * Get the destroy response of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  stirng  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function toDestroyResponse(Request $request, string $id): RedirectResponse
    {
        $model = $this->resolveRouteBinding($id);

        $action = (class_uses_recursive(SoftDeletes::class) && $model->trashed()) ? 'forceDelete' : 'delete';

        if ($this->getPolicy() && $request->user()->cannot($action, $model)) {
            abort(RedirectResponse::HTTP_FORBIDDEN);
        }

        $this->handleDestroy($request, $model);

        return Redirect::route('root.resource.index', $this->getKey());
    }

    /**
     * Handle the action request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handleAction(Request $request): RedirectResponse
    {
        $action = $this->resolveActions($request)
                    ->filterVisible($request, $request->boolean('individual') ? static::SHOW : static::INDEX)
                    ->resolveFromRequest($request);

        return $action->perform($request, $this);
    }
}
