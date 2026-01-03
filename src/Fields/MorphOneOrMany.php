<?php

declare(strict_types=1);

namespace Cone\Root\Fields;

use Closure;
use Cone\Root\Fields\Select as SelectField;
use Cone\Root\Filters\Select;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOneOrMany as EloquentRelation;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Uri;

/**
 * @template TRelation of \Illuminate\Database\Eloquent\Relations\MorphOneOrMany
 *
 * @extends \Cone\Root\Fields\HasOneOrMany<TRelation>
 */
abstract class MorphOneOrMany extends HasOneOrMany
{
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
     * Get the morph types.
     */
    public function getTypes(): array
    {
        return $this->types;
    }

    /**
     * {@inheritdoc}
     */
    public function getSearchableColumns(): array
    {
        return match (true) {
            array_is_list($this->searchableColumns) => array_fill_keys($this->types, $this->searchableColumns),
            default => $this->searchableColumns,
        };
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
            array_is_list($columns) => array_fill_keys($this->getTypes(), $columns),
            default => $columns,
        };

        return parent::searchable($value, $callback, $columns);
    }

    /**
     * {@inheritdoc}
     */
    public function filters(Request $request): array
    {
        $typeFilter = new class($this) extends Select
        {
            public function __construct(protected MorphOneOrMany $field)
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
                    ->options(function (Request $request, Model $model): array {
                        $related = $this->field->getRelation($model)->getRelated()::class;

                        return array_replace(
                            $this->options($request),
                            [$related => __(Str::of($related)->classBasename()->headline()->value())],
                        );
                    });
            }
        };

        return array_merge([$typeFilter], parent::filters($request));
    }

    /**
     * Set the morph types.
     */
    public function types(array $types): static
    {
        $this->types = array_merge($this->types, $types);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(Request $request, Model $model): array
    {
        $data = parent::toInput($request, $model);

        if (! empty($data['url'])) {
            $relation = $this->getRelation($model);

            $data['url'] = Uri::of($data['url'])
                ->withQuery([$relation->getMorphType() => $relation->getRelated()::class])
                ->value();
        }

        return $data;
    }
}
