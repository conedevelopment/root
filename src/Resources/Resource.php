<?php

namespace Cone\Root\Resources;

use Closure;
use Cone\Root\Fields\Field;
use Cone\Root\Support\Collections\Actions;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Support\Collections\Filters;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

class Resource implements Arrayable
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
     * Create a new resource instance.
     *
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
        return Str::of($this->getModel())->classBasename()->lower()->plural()->kebab();
    }

    /**
     * Get the name.
     *
     * @return string
     */
    public function getName(): string
    {
        return Str::of($this->getModel())->classBasename()->plural();
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
     * @param  string  $value
     * @return \Illuminate\Database\Eloquent\Model|null
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function resolveRouteBinding(string $id): ?Model
    {
        $model = $this->getModelInstance()->resolveRouteBinding($id);

        if (is_null($model)) {
            throw new ModelNotFoundException();
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
     * @param  array|\Closure  $callback
     * @return $this
     */
    public function withFields(array|Closure $callback): static
    {
        if (is_array($callback)) {
            $callback = static function () use ($callback) {
                return $callback;
            };
        }

        $this->fieldsResolver = $callback;

        return $this;
    }

    /**
     * Collect the resolved fields.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Fields
     */
    protected function collectFields(Request $request): Fields
    {
        $fields = Fields::make($this->fields($request));

        if (! is_null($this->fieldsResolver)) {
            $fields = $fields->merge(call_user_func_array($this->fieldsResolver, [$request]));
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
     * @param  array|\Closure  $callback
     * @return $this
     */
    public function withFilters(array|Closure $callback): static
    {
        if (is_array($callback)) {
            $callback = static function () use ($callback) {
                return $callback;
            };
        }

        $this->filtersResolver = $callback;

        return $this;
    }

    /**
     * Collect the resolved filters.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Filters
     */
    protected function collectFilters(Request $request): Filters
    {
        $filters = Filters::make($this->filters($request));

        if (! is_null($this->filtersResolver)) {
            $filters = $filters->merge(call_user_func_array($this->filtersResolver, [$request]));
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
     * @param  array|\Closure  $callback
     * @return $this
     */
    public function withActions(array|Closure $callback): static
    {
        if (is_array($callback)) {
            $callback = static function () use ($callback) {
                return $callback;
            };
        }

        $this->actionsResolver = $callback;

        return $this;
    }

    /**
     * Collect the resolved actions.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Actions
     */
    protected function collectActions(Request $request): Actions
    {
        $actions = Actions::make($this->actions($request));

        if (! is_null($this->actionsResolver)) {
            $actions = $actions->merge(call_user_func_array($this->actionsResolver, [$request]));
        }

        return $actions;
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
            'index' => URL::route('root.resource.index', $this->getKey()),
            'create' => URL::route('root.resource.create', $this->getKey()),
        ];
    }

    /**
     * Map the abilities.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function mapAbilities(Request $request): array
    {
        $policy = Gate::getPolicyFor($this->getModel());

        $abilities = ['viewAny', 'create'];

        return array_reduce($abilities, function (array $stack, $ability) use ($request, $policy): array {
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
        $filters = $this->collectFilters($request);

        $fields = $this->collectFields($request)->filterVisibleFor($request, static::INDEX);

        $query = $filters->apply($this->query(), $request)
                    ->latest()
                    ->paginate($request->input('per_page'))
                    ->withQueryString()
                    ->tap(function (LengthAwarePaginator $paginator) use ($request, $fields) {
                        $paginator->getCollection()->transform(function (Model $model) use ($request, $fields): array {
                            return $model->toResourceDisplay($request, $this, $fields);
                        });
                    });

        return array_merge($this->toArray(), [
            'query' => $query,
            'filters' => $filters,
            'actions' => $this->collectActions($request)->filterVisibleFor($request, static::INDEX),
        ]);
    }

    /**
     * Get the create representation of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toCreate(Request $request): array
    {
        $fields = $this->collectFields($request)->filterVisibleFor($request, static::CREATE);

        return array_merge($this->toArray(), [
            'model' => $this->getModelInstance()->newInstance()->toResourceForm($request, $this, $fields),
        ]);
    }

    /**
     * Get the show representation of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return array
     */
    public function toShow(Request $request, string $id): array
    {
        $fields = $this->collectFields($request)->filterVisibleFor($request, static::SHOW);

        $model = $this->resolveRouteBinding($id);

        return array_merge($this->toArray(), [
            'model' => $model->toResourceDisplay($request, $this, $fields),
            'actions' => $this->collectActions($request)->filterVisibleFor($request, static::SHOW),
        ]);
    }

    /**
     * Get the edit representation of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @return array
     */
    public function toEdit(Request $request, string $id): array
    {
        $fields = $this->collectFields($request)->filterVisibleFor($request, static::UPDATE);

        $model = $this->resolveRouteBinding($id);

        return array_merge($this->toArray(), [
            'model' => $model->toResourceForm($request, $this, $fields),
        ]);
    }

    /**
     * Handle the store request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model
     */
    public function handleStore(Request $request): Model
    {
        $fields = $this->collectFields($request)->filterVisibleFor($request, static::CREATE);

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
     * Handle the update request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @param  \Illuminate\Database\Eloquent\Model
     */
    public function handleUpdate(Request $request, string $id): Model
    {
        $fields = $this->collectFields($request)->filterVisibleFor($request, static::UPDATE);

        $model = $this->resolveRouteBinding($id);

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
     * Handle the destroy request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $id
     * @param  \Illuminate\Database\Eloquent\Model
     */
    public function handleDestroy(Request $request, string $id): Model
    {
        $model = $this->resolveRouteBinding($id);

        // Implement forceDelete

        $model->delete();

        return $model;
    }
}
