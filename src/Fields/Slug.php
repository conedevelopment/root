<?php

namespace Cone\Root\Fields;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Slug extends Text
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.slug';

    /**
     * The attributes that the slug is created from.
     */
    protected array $from = ['id'];

    /**
     * The slug separator.
     */
    protected string $separator = '-';

    /**
     * Indicates if the slug should be unique.
     */
    protected bool $unique = false;

    /**
     * Indicates if the slug field is nullable.
     */
    protected bool $nullable = false;

    /**
     * The slug resolver.
     */
    protected ?Closure $generatorResolver = null;

    /**
     * Create a new field instance.
     */
    public function __construct(string $label = null, Closure|string $modelAttribute = null)
    {
        parent::__construct($label ?: __('Slug'), $modelAttribute ?: 'slug');

        $this->readonly();

        $this->unique();
    }

    /**
     * Set the "nullable" property.
     */
    public function nullable(bool $value = true): static
    {
        $this->nullable = $value;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function persist(Request $request, Model $model, mixed $value): void
    {
        if (! $model->exists) {
            $model->saved(function (Model $model) use ($request): void {
                $value = $this->generate($request, $model);

                $this->resolveHydrate($request, $model, $value);

                Model::withoutEvents(static function () use ($model): void {
                    $model->save();
                });
            });
        }

        parent::persist($request, $model, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function getValueForHydrate(Request $request): mixed
    {
        $value = parent::getValueForHydrate($request);

        if (! $this->nullable && empty($value)) {
            $value = Str::random();
        }

        return Str::slug($value, $this->separator);
    }

    /**
     * Set the "from" property.
     */
    public function from(array|string $attributes): static
    {
        $this->from = (array) $attributes;

        return $this;
    }

    /**
     * Set the "unique" property.
     */
    public function unique(bool $value = true): static
    {
        $this->unique = $value;

        if ($value) {
            $this->createRules(static function (Request $request, Model $model): array {
                return [Rule::unique($model->getTable())];
            })->updateRules(static function (Request $request, Model $model): array {
                return [Rule::unique($model->getTable())->ignoreModel($model)];
            });
        }

        return $this;
    }

    /**
     * Set the generator resolve callback.
     */
    public function generateUsing(Closure $callback): static
    {
        $this->generatorResolver = $callback;

        return $this;
    }

    /**
     * Generate the slug.
     */
    protected function generate(Request $request, Model $model): string
    {
        $value = Str::of(implode($this->separator, $model->only($this->from)))
            ->slug($this->separator)
            ->value();

        if (! is_null($this->generatorResolver)) {
            return call_user_func_array($this->generatorResolver, [$request, $model, $value]);
        }

        if (! $this->unique) {
            return $value;
        }

        $match = $model
            ->newQuery()
            ->when(
                in_array(SoftDeletes::class, class_uses_recursive($model)),
                static function (Builder $query): Builder {
                    return $query->withTrashed();
                }
            )
            ->whereRaw(sprintf(
                "`%s` regexp '^%s(%s[\\\\d]+)?$'",
                $this->modelAttribute,
                preg_quote($value),
                preg_quote($this->separator)
            ))
            ->orderByDesc($this->modelAttribute)
            ->limit(1)
            ->value($this->modelAttribute);

        $value = is_null($match) ? $value : preg_replace_callback(
            sprintf('/%s([\d]+)?$/', preg_quote($this->separator)),
            static function (array $match): string {
                return str_replace($match[1], (string) (((int) $match[1]) + 1), $match[0]);
            },
            $match
        );

        return $value === $match ? sprintf('%s%s1', $value, $this->separator) : $value;
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'help' => $this->help ?: __('Leave it empty for auto-generated slug.'),
        ]);
    }
}
