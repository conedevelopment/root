<?php

namespace Cone\Root\Resources;

use Closure;
use Cone\Root\Http\Requests\CreateRequest;
use Cone\Root\Http\Requests\IndexRequest;
use Cone\Root\Http\Requests\ShowRequest;
use Cone\Root\Http\Requests\UpdateRequest;
use Cone\Root\Interfaces\Registries\Item;
use Cone\Root\Support\Collections\Actions;
use Cone\Root\Support\Collections\Extracts;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Support\Collections\Filters;
use Cone\Root\Support\Collections\Widgets;
use Cone\Root\Traits\Authorizable;
use Cone\Root\Traits\StoresReferences;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Resource implements Arrayable, Item
{
    use Authorizable;
    use StoresReferences;

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
     * The resolved store.
     *
     * @var array
     */
    protected array $resolved = [];

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
        return new ($this->getModel());
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
        if (! isset($this->resolved['fields'])) {
            $fields = Fields::make($this->fields($request));

            if (! is_null($this->fieldsResolver)) {
                $fields = call_user_func_array($this->fieldsResolver, [$request, $fields]);
            }

            $this->resolved['fields'] = $fields;
        }

        return $this->resolved['fields'];
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
        if (! isset($this->resolved['filters'])) {
            $filters = Filters::make($this->filters($request));

            if (! is_null($this->filtersResolver)) {
                $filters = call_user_func_array($this->filtersResolver, [$request, $filters]);
            }

            $this->resolved['filters'] = $filters;
        }

        return $this->resolved['filters'];
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
        if (! isset($this->resolved['actions'])) {
            $actions = Actions::make($this->actions($request));

            if (! is_null($this->actionsResolver)) {
                $actions = call_user_func_array($this->actionsResolver, [$request, $actions]);
            }

            $this->resolved['actions'] = $actions;
        }

        return $this->resolved['actions'];
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
        if (! isset($this->resolved['extracts'])) {
            $extracts = Extracts::make($this->extracts($request));

            if (! is_null($this->extractsResolver)) {
                $extracts = call_user_func_array($this->extractsResolver, [$request, $extracts]);
            }

            $this->resolved['extracts'] = $extracts;
        }

        return $this->resolved['extracts'];
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
        if (! isset($this->resolved['widgets'])) {
            $widgets = Widgets::make($this->widgets($request));

            if (! is_null($this->widgetsResolver)) {
                $widgets = call_user_func_array($this->widgetsResolver, [$request, $widgets]);
            }

            $this->resolved['widgets'] = $widgets;
        }

        return $this->resolved['widgets'];
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
     * @param  \Cone\Root\Http\Requests\IndexRequest  $request
     * @return array
     */
    public function toIndex(IndexRequest $request): array
    {
        $filters = $this->resolveFilters($request)->available($request);

        $fields = $this->resolveFields($request)->available($request);

        $query = $filters->apply($request, $this->query())
                    ->latest()
                    ->paginate($request->input('per_page'))
                    ->withQueryString()
                    ->through(function (Model $model) use ($request, $fields): array {
                        return $model->toResourceDisplay($request, $this, $fields);
                    });

        return array_merge($this->toArray(), [
            'actions' => $this->resolveActions($request)->available($request)->toArray(),
            'extracts' => $this->resolveExtracts($request)->toArray(),
            'filters' => $filters->toArray(),
            'query' => $query->toArray(),
            'widgets' => $this->resolveWidgets($request)->available($request)->toArray(),
        ]);
    }

    /**
     * Get the create representation of the resource.
     *
     * @param  \Cone\Root\Http\Requests\CreateRequest  $request
     * @return array
     */
    public function toCreate(CreateRequest $request): array
    {
        $fields = $this->resolveFields($request)->available($request);

        return array_merge($this->toArray(), [
            'model' => $this->getModelInstance()->toResourceForm($request, $this, $fields),
        ]);
    }

    /**
     * Get the show representation of the resource.
     *
     * @param  \Cone\Root\Http\Requests\ShowRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toShow(ShowRequest $request, Model $model): array
    {
        $fields = $this->resolveFields($request)->available($request);

        return array_merge($this->toArray(), [
            'actions' => $this->resolveActions($request)->available($request)->toArray(),
            'model' => $model->toResourceDisplay($request, $this, $fields),
        ]);
    }

    /**
     * Get the edit representation of the resource.
     *
     * @param  \Cone\Root\Http\Requests\UpdateRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toEdit(UpdateRequest $request, Model $model): array
    {
        $fields = $this->resolveFields($request)->available($request);

        return array_merge($this->toArray(), [
            'model' => $model->toResourceForm($request, $this, $fields),
        ]);
    }

    /**
     * Handle the resource registered event.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function registered(Request $request): void
    {
        $this->resolveActions($request)->resolved($request, $this, $this->getKey());
        $this->resolveExtracts($request)->resolved($request, $this, $this->getKey());
        $this->resolveFields($request)->resolved($request, $this, $this->getKey());
        $this->resolveWidgets($request)->resolved($request, $this, $this->getKey());
    }
}
