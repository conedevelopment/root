<?php

namespace Cone\Root\Fields;

use Closure;
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

                    $options = is_null($model->language)
                        ? $options
                        : array_unique(array_merge([$model->language], $options));

                    return array_combine($options, $options);
                })
                ->required()
                ->rules(['required', 'string', Rule::in($this->getLanguages())]),
        ];
    }
}
