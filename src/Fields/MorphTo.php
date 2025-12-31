<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Fields\Select as SelectField;
use Cone\Root\Filters\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo as EloquentRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * @extends \Cone\Root\Fields\BelongsTo<\Illuminate\Database\Eloquent\Relations\MorphTo>
 */
class MorphTo extends BelongsTo
{
    /**
     * The Blade template.
     */
    protected string $template = 'root::fields.relation';

    /**
     * Indicates whether the field is async.
     */
    protected bool $async = true;

    /**
     * The morph types.
     */
    protected array $types = [];

    /**
     * {@inheritdoc}
     */
    public function getRelation(Model $model): EloquentRelation
    {
        return parent::getRelation($model);
    }

    /**
     * {@inheritdoc}
     */
    public function async(bool $value = true): static
    {
        return $this;
    }

    /**
     * Get the morph types.
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * {@inheritdoc}
     */
    public function filters(Request $request): array
    {
        $typeFilter = new class($this) extends Select
        {
            public function __construct(protected MorphTo $field)
            {
                parent::__construct('type');
            }

            public function getName(): string
            {
                return __('Type');
            }

            public function apply(Request $request, Builder $query, mixed $value): Builder
            {
                return $query;
            }

            public function options(Request $request): array
            {
                return array_map(
                    static function (string $type): string {
                        return __(Str::of($type)->classBasename()->headline()->value());
                    },
                    array_combine($this->field->getTypes(), $this->field->getTypes())
                );
            }

            public function toField(): SelectField
            {
                return parent::toField()
                    ->value(function (Request $request, Model $model): ?string {
                        return $model->getAttribute($this->field->getRelation($model)->getMorphType());
                    });
            }
        };

        return array_merge([$typeFilter], parent::filters($request));
    }

    /**
     * {@inheritdoc}
     */
    public function resolveRelatableQuery(Request $request, Model $model): Builder
    {
        $relation = $this->getRelation($model);

        $type = Str::before((string) $this->getOldValue($request), ':') ?: $request->query($relation->getMorphType());

        $model->setAttribute(
            $relation->getMorphType(),
            $type ?: $model->getAttribute($relation->getMorphType()) ?: ($this->types[0] ?? null)
        );

        return parent::resolveRelatableQuery($request, $model);
    }

    /**
     * Map the async searchable fields.
     */
    protected function mapAsyncSearchableFields(Request $request): Fields
    {
        $fields = new Fields;

        foreach ($this->getSearchableColumns() as $type => $columns) {
            foreach ($columns as $column) {
                $field = Hidden::make($this->getRelationName(), sprintf('%s:%s', $type, $column))
                    ->searchable(callback: function (Request $request, Builder $query, mixed $value, string $attribute): Builder {
                        [$type, $column] = explode(':', $attribute);

                        return match ($query->getModel()::class) {
                            $type => $query->where($query->qualifyColumn($column), 'like', "%{$value}%", 'or'),
                            default => $query,
                        };
                    });

                $fields->push($field);
            }
        }

        return $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function searchable(bool|Closure $value = true, ?Closure $callback = null, array $columns = ['id']): static
    {
        $columns = match (true) {
            array_is_list($columns) => array_fill_keys($this->types, $columns),
            default => $columns,
        };

        return parent::searchable($value, $callback, $columns);
    }

    /**
     * Resolve the filter query.
     */
    public function resolveSearchQuery(Request $request, Builder $query, mixed $value): Builder
    {
        if (! $this->isSearchable()) {
            return parent::resolveSearchQuery($request, $query, $value);
        }

        return call_user_func_array($this->searchQueryResolver, [
            $request, $query, $value, $this->getSearchableColumns(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function resolveHydrate(Request $request, Model $model, mixed $value): void
    {
        $value = is_null($value) ? $value : explode(':', $value);

        $related = match (true) {
            ! is_null($value) => tap(new $value[0], static function (Model $related) use ($value): void {
                $related->forceFill([$related->getKeyName() => $value[1]]);
            }),
            default => $value,
        };

        parent::resolveHydrate($request, $model, $related);
    }

    /**
     * {@inheritdoc}
     */
    public function newOption(Model $related, string $label): Option
    {
        return new Option(
            sprintf('%s:%s', $related::class, $related->getKey()),
            sprintf('%s (%s)', $label, __(Str::of($related::class)->classBasename()->headline()->value()))
        );
    }

    /**
     * Set the morph types.
     */
    public function types(array $types): static
    {
        $this->types = $types;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'url' => sprintf('%s/search', $this->replaceRoutePlaceholders($request->route())),
            'morphTypeName' => $name = $this->getRelation($model)->getMorphType(),
            'morphType' => $model->getAttribute($name),
        ]);
    }
}
