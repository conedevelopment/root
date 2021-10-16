<?php

namespace Cone\Root\Resources;

use Closure;
use Cone\Root\Support\Collections\Fields;
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
                $ability => is_null($policy) || $request?->user()->can($ability, $this->getModel()),
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
            'urls' => App::call(function (Request $request): array {
                return $this->mapUrls($request);
            }),
            'abilities' => App::call(function (Request $request): array {
                return $this->mapAbilities($request);
            }),
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
        $query = $this->query()
                    ->paginate($request->input('per_page'))
                    ->withQueryString();

        $fields = $this->collectFields($request);

        $query->getCollection()->transform(function (Model $model) use ($request, $fields): array {
            return $model->toResourceDisplay($request, $this, $fields);
        });

        return array_merge($this->toArray(), [
            'query' => $query,
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

        return $model->toResourceDisplay($request, $this, $fields);
    }
}
