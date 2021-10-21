<?php

namespace Cone\Root\Resources;

use Closure;
use Cone\Root\Fields\Field;
use Cone\Root\Support\Collections\Fields;
use Cone\Root\Support\Collections\Filters;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
     * @var \Closure
     */
    protected Closure $fieldsResolver;

    /**
     * The filters resolver.
     *
     * @var \Closure
     */
    protected Closure $filtersResolver;

    /**
     * Create a new resource instance.
     *
     * @return void
     */
    public function __construct(string $model)
    {
        $this->model = $model;

        $this->fieldsResolver = static function (): array {
            return [];
        };

        $this->filtersResolver = static function (): array {
            return [];
        };
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
     * Get the model instance of the query.
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
     * @param  \Closure  $callback
     * @return $this
     */
    public function withFields(Closure $callback): self
    {
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
        return Fields::make($this->fields($request))
                    ->merge(call_user_func_array($this->fieldsResolver, [$request]));
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
     * @param  \Closure  $callback
     * @return $this
     */
    public function withFiilters(Closure $callback): self
    {
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
        return Filters::make($this->filters($request))
                    ->merge(call_user_func_array($this->filtersResolver, [$request]));
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

        $query = $filters->apply($this->query(), $request)
                        ->latest()
                        ->paginate($request->input('per_page'))
                        ->withQueryString();

        $fields = $this->collectFields($request);

        $query->getCollection()->transform(function (Model $model) use ($request, $fields): array {
            return $model->toResourceDisplay($request, $this, $fields);
        });

        return array_merge($this->toArray(), [
            'query' => $query,
            'filters' => $filters,
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
        $fields = $this->collectFields($request);

        return array_merge($this->toArray(), [
            'model' => $this->getModelInstance()->toResourceForm($request, $this, $fields),
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
        $fields = $this->collectFields($request);

        $model = $this->getModelInstance()->resolveRouteBinding($id);

        return array_merge($this->toArray(), [
            'model' => $model->toResourceDisplay($request, $this, $fields),
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
        $fields = $this->collectFields($request);

        $model = $this->getModelInstance()->resolveRouteBinding($id);

        return array_merge($this->toArray(), [
            'model' => $model->toResourceForm($request, $this, $fields),
        ]);
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
        $fields = $this->collectFields($request);

        $model = $this->getModelInstance()->resolveRouteBinding($id);

        $request->validate(
            $fields->mapToValidate($request, $model, static::UPDATE)->toArray()
        );

        $fields->each(static function (Field $field) use ($request, $model): void {
            $field->hydrate($request, $model, $request->input($field->name));
        });

        $model->save();

        return $model;
    }
}
