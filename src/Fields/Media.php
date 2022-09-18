<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Fields\Field;
use Cone\Root\Filters\Search;
use Cone\Root\Http\Controllers\MediaController;
use Cone\Root\Http\Requests\ResourceRequest;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Models\Medium;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Routing\Router;
use Illuminate\Support\Collection;

class Media extends MorphToMany
{
    /**
     * The searchable columns.
     *
     * @var array
     */
    protected array $searchableColumns = ['file_name'];

    /**
     * Indicates if the component is async.
     *
     * @var bool
     */
    protected bool $async = true;

    /**
     * Indicates if multiple items can be selected.
     *
     * @var bool
     */
    protected bool $multiple = true;

    /**
     * The Vue component.
     *
     * @var string
     */
    protected string $component = 'Media';

    /**
     * The storing resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $storingResolver = null;

    /**
     * {@inheritdoc}
     */
    public function async(bool $value = true): static
    {
        return $this;
    }

    /**
     * Set the multiple attribute.
     *
     * @param  bool  $value
     * @return $this
     */
    public function multiple(bool $value = true): static
    {
        $this->multiple = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function asSubResource(bool $value = true): static
    {
        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function searchable(bool|Closure $value = true, array $columns = ['id']): static
    {
        $this->searchableColumns = $columns;

        return parent::searchable(false);
    }

    /**
     * Set the storing resolver callback.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function storeUsing(Closure $callback): static
    {
        $this->storingResolver = $callback;

        return $this;
    }

    /**
     * Store the file using the given path and request.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  string  $path
     * @return \Cone\Root\Models\Medium
     */
    public function store(RootRequest $request, string $path): Medium
    {
        $medium = (Medium::proxy())::makeFrom($path);

        if (! is_null($this->storingResolver)) {
            call_user_func_array($this->storingResolver, [$request, $medium, $path]);
        }

        $request->user()->uploads()->save($medium);

        return $medium;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(RootRequest $request, Model $model): void
    {
        $model->saved(function (Model $model) use ($request): void {
            $value = $this->getValueForHydrate($request, $model);

            $this->resolveHydrate($request, $model, $value);

            $this->getRelation($model)->sync($value);
        });
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(RootRequest $request, Model $model, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (RootRequest $request, Model $model, mixed $value): void {
                $relation = $this->getRelation($model);

                $results = $this->resolveQuery($request, $model)
                                ->findMany(array_keys($value))
                                ->each(static function (Model $related) use ($relation, $value): void {
                                    $related->setRelation(
                                        $relation->getPivotAccessor(),
                                        $relation->newPivot($value[$related->getKey()])
                                    );
                                });

                $model->setRelation($relation->getRelationName(), $results);
            };
        }

        parent::resolveHydrate($request, $model, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function fields(RootRequest $request): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function filters(RootRequest $request): array
    {
        $fields = new Fields(array_map(static function (string $column): Field {
            return new Text($column, $column);
        }, $this->getSearchableColumns()));

        return array_values(array_filter([
            $fields->isNotEmpty() ? Search::make($fields) : null,
        ]));
    }

    /**
     * {@inheritdoc}
     */
    public function resolveOptions(RootRequest $request, Model $model): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function resolveValue(RootRequest $request, Model $model): mixed
    {
        if (is_null($this->valueResolver)) {
            $this->valueResolver = function (RootRequest $request, Model $model, mixed $value): array {
                return $value->mapWithKeys(function (Model $related) use ($request, $model): array {
                    return [
                        $related->getKey() => $this->mapPivotValues($request, $model, $related),
                    ];
                })->toArray();
            };
        }

        return parent::resolveValue($request, $model);
    }

    /**
     * Map the pivot values.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  \Illuminate\Database\Eloquent\Model  $related
     * @return array
     */
    protected function mapPivotValues(RootRequest $request, Model $model, Model $related): array
    {
        $relation = $this->getRelation($model);

        return $this->resolveFields($request)
                    ->available($request, $model, $related)
                    ->mapWithKeys(static function (Field $field) use ($request, $related, $relation): array {
                        return [
                            $field->name => $field->resolveValue(
                                $request, $related->getRelation($relation->getPivotAccessor())
                            ),
                        ];
                    })
                    ->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function mapOption(RootRequest $request, Model $model, Model $related): array
    {
        $relation = $this->getRelation($model);

        $pivot = $related->relationLoaded($relation->getPivotAccessor())
            ? $related->getRelation($relation->getPivotAccessor())
            : $relation->newPivot();

        return array_merge(
            parent::mapOption($request, $model, $related),
            $related->append(['dimensions', 'formatted_size'])->toArray(),
            [
                'fields' => $this->resolveFields($request)
                                ->available($request, $model, $related)
                                ->mapToForm($request, $pivot)
                                ->toArray(),
                'formatted_created_at' => $related->created_at->format('Y-m-d H:i'),
            ],
        );
    }

    /**
     * Map the items.
     *
     * @param  \Cone\Root\Http\Requests\ResourceRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function mapItems(ResourceRequest $request, Model $model): array
    {
        $filters = $this->resolveFilters($request)->available($request);

        $query = $this->resolveQuery($request, $model);

        $items = $filters->apply($request, $query)
                        ->latest()
                        ->paginate($request->input('per_page'))
                        ->withQueryString()
                        ->setPath($this->resolveUri($request))
                        ->through(function (Model $related) use ($request, $model): array {
                            return $this->mapOption($request, $model, $related);
                        })
                        ->toArray();

        return array_merge($items, [
            'query' => $filters->mapToQuery($request, $this->resolveQuery($request, $model)),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function routes(Router $router): void
    {
        $router->get('/', [MediaController::class, 'index']);
        $router->post('/', [MediaController::class, 'store']);
        $router->delete('/', [MediaController::class, 'destroy']);
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        $models = $this->getValue($request, $model);

        $relation = $this->getRelation($model);

        return array_merge(parent::toInput($request, $model), [
            'fields' => $models->mapWithKeys(function (Model $related) use ($request, $model, $relation): array {
                return [
                    $related->getKey() => $this->resolveFields($request)
                                            ->available($request, $model, $related)
                                            ->mapToForm($request, $related->getRelation($relation->getPivotAccessor()))
                                            ->toArray(),
                ];
            }),
            'filters' => $this->resolveFilters($request)->available($request, $model)->mapToForm($request)->toArray(),
            'multiple' => $this->multiple,
            'url' => $this->resolveUri($request),
            'selection' => $models->map(function (Model $related) use ($request, $model): array {
                return $this->mapOption($request, $model, $related);
            }),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function toValidate(RootRequest $request, Model $model): array
    {
        $pivotRules = $this->resolveFields($request)
                            ->available($request, $model)
                            ->mapToValidate($request, $model);

        return array_merge(
            parent::toValidate($request, $model),
            Collection::make($pivotRules)
                    ->mapWithKeys(function (array $rules, string $key): array {
                        return [sprintf('%s.*.%s', $this->name, $key) => $rules];
                    })
                    ->toArray(),
        );
    }
}
