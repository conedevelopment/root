<?php

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Models\TranslationValue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class Translations extends MorphMany
{
    /**
     * The default languages.
     */
    protected static array $defaultLanguages = [];

    /**
     * The field specific languages.
     */
    protected array $languages = [];

    /**
     * Indicates whether the relation is a sub resource.
     */
    protected bool $asSubResource = true;

    /**
     * The relations to eager load on every query.
     */
    protected array $with = [
        'values',
    ];

    /**
     * Create a new relation field instance.
     */
    public function __construct(?string $label = null, Closure|string|null $modelAttribute = 'translations', Closure|string|null $relation = null)
    {
        parent::__construct($label ?: __('Translations'), $modelAttribute, $relation);

        $this->hiddenOn(['index']);
    }

    /**
     * Set the default languages.
     */
    public static function defaultLanguages(array $languages): void
    {
        static::$defaultLanguages = $languages;
    }

    /**
     * Set the field specific languages.
     */
    public function languages(array $languages): static
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * Get the available languages.
     */
    public function getLanguages(): array
    {
        return $this->languages ?: static::$defaultLanguages;
    }

    /**
     * Resolve the display format or the query result.
     */
    public function resolveDisplay(Model $related): ?string
    {
        if (is_null($this->displayResolver)) {
            $this->display('language');
        }

        return parent::resolveDisplay($related);
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, Model $model, mixed $value): void
    {
        $this->resolveHydrate($request, $model, $value);

        $model->saved(static function (Model $model): void {
            $model->values->each(function (TranslationValue $value) use ($model): void {
                $value->translation()->associate($model)->save();
            });
        });
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        if (is_null($this->hydrateResolver)) {
            $this->hydrateResolver = function (Request $request, Model $model, array $value): void {
                foreach ($value as $key => $attr) {
                    if ($key === 'language') {
                        $model->setAttribute($key, $attr);
                    } else {
                        $related = $model->values->firstWhere('key', $key) ?: $model->values()->make();

                        $cast = $model->related->getCasts()[$key] ?? 'string';

                        $related->mergeCasts(['value' => $cast])->fill(['key' => $key, 'value' => $attr]);

                        $model->values->when(
                            ! $related->exists,
                            fn (Collection $collection): Collection => $collection->push($related)
                        );
                    }
                }
            };
        }

        parent::resolveHydrate($request, $model, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForHydrate(Request $request): mixed
    {
        return $this->resolveFields($request)
            ->mapWithKeys(static function (Field $field) use ($request): mixed {
                return [
                    $field->getModelAttribute() => $field->getValueForHydrate($request),
                ];
            })
            ->all();
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            Select::make(__('Language'), 'language')
                ->options(function (Request $request, Model $model): array {
                    $languages = $this->getLanguages();

                    $options = array_diff(
                        $languages,
                        $model->related->translations->pluck('language')->all()
                    );

                    return is_null($model->language)
                        ? $options
                        : array_unique(array_merge([$model->language], $options));
                })
                ->required()
                ->rules(['required', 'string', Rule::in($this->getLanguages())]),
        ];
    }
}
