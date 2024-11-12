<?php

namespace Cone\Root\Fields;

use Closure;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class Translations extends MorphMany
{
    /**
     * The default locales.
     */
    protected static array $defaultLocales = [];

    /**
     * The field specific locales.
     */
    protected array $locales = [];

    /**
     * Indicates whether the relation is a sub resource.
     */
    protected bool $asSubResource = true;

    /**
     * Create a new relation field instance.
     */
    public function __construct(?string $label = null, Closure|string|null $modelAttribute = 'translations', Closure|string|null $relation = null)
    {
        parent::__construct($label ?: __('Translations'), $modelAttribute, $relation);

        $this->hiddenOn(['index']);
    }

    /**
     * Set the default locales.
     */
    public static function defaultLocales(array $locales): void
    {
        static::$defaultLocales = $locales;
    }

    /**
     * Set the field specific locales.
     */
    public function locales(array $locales): static
    {
        $this->locales = $locales;

        return $this;
    }

    /**
     * Get the available locales.
     */
    public function getLocales(): array
    {
        return $this->locales ?: static::$defaultLocales;
    }

    /**
     * Resolve the display format or the query result.
     */
    public function resolveDisplay(Model $related): ?string
    {
        if (is_null($this->displayResolver)) {
            $this->display('locale');
        }

        return parent::resolveDisplay($related);
    }

    /**
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            Select::make(__('Locale'), 'locale')
                ->options(function (Request $request, Model $model): array {
                    $locales = $this->getLocales();

                    $options = array_diff(
                        $locales,
                        $model->related->translations->pluck('locale')->all()
                    );

                    $options = is_null($model->locale)
                        ? $options
                        : array_unique(array_merge([$model->locale], $options));

                    return array_combine($options, array_map('strtoupper', $options));
                })
                ->required()
                ->rules(['required', 'string', Rule::in(array_keys($this->getLocales()))]),
        ];
    }
}
