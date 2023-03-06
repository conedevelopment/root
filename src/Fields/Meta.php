<?php

namespace Cone\Root\Fields;

use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Models\FieldsetModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class Meta extends MorphMany
{
    /**
     * The Vue component.
     */
    protected string $component = 'Fieldset';

    /**
     * Create a new meta field instance.
     */
    public function __construct(string $label, string $name = 'metas')
    {
        parent::__construct($label, $name);
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
    public function async(bool $value = true): static
    {
        return $this;
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
    public function getValue(RootRequest $request, Model $model): mixed
    {
        $value = parent::getValue($request, $model);

        if (is_null($value)) {
            return $value;
        }

        $fields = $this->resolveFields($request)->available($request, $model)->map->getKey()->toArray();

        return $value->filter(static function (Model $related) use ($fields): bool {
            return in_array($related->getAttribute('key'), $fields);
        })->values();
    }

    /**
     * {@inheritdoc}
     */
    public function resolveValue(RootRequest $request, Model $model): mixed
    {
        if (is_null($this->valueResolver)) {
            $this->valueResolver = static function (RootRequest $request, Model $model, mixed $value): mixed {
                if ($value instanceof Collection) {
                    return $value->mapWithKeys(static function (Model $related): array {
                        return [$related->getAttribute('key') => $related->getAttribute('value')];
                    })->toArray();
                }

                return $value;
            };
        }

        return parent::resolveValue($request, $model);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(RootRequest $request, Model $model, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (RootRequest $request, Model $model, mixed $value): void {
                $relation = $this->getRelation($model);

                $query = $relation->getQuery();

                $models = $query->whereIn($query->qualifyColumn('key'), $keys = array_keys($value))->get();

                $models->each(static function (Model $related) use ($value): void {
                    $related->setAttribute('value', $value[$related->getAttribute('key')] ?? null);
                });

                foreach (array_diff($keys, $models->pluck('key')->toArray()) as $key) {
                    $models->push($relation->make(['key' => $key, 'value' => $value[$key]]));
                }

                $model->setRelation($this->name, $models);
            };
        }

        parent::resolveHydrate($request, $model, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request, Model $model): array
    {
        $data = parent::toInput($request, $model);

        $data['value'] = (array) $data['value'];

        $json = FieldsetModel::make()
                    ->setRelation('parent', $model)
                    ->forceFill($data['value']);

        $fields = $this->resolveFields($request)
                    ->available($request, $model)
                    ->mapToForm($request, $json)
                    ->toArray();

        return array_replace_recursive($data, [
            'fields' => $fields,
            'formatted_value' => array_column($fields, 'formatted_value', 'name'),
            'value' => array_column($fields, 'value', 'name'),
        ]);
    }

    /**
     * Get the validation representation of the field.
     */
    public function toValidate(RootRequest $request, Model $model): array
    {
        $rules = $this->resolveFields($request)
                    ->available($request, $model)
                    ->mapToValidate($request, $model);

        return array_merge(
            parent::toValidate($request, $model),
            Collection::make($rules)
                    ->mapWithKeys(function (array $rules, string $key): array {
                        return [sprintf('%s.%s', $this->getKey(), $key) => $rules];
                    })
                    ->toArray(),
        );
    }
}
