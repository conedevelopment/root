<?php

namespace Cone\Root\Filters;

use Cone\Root\Columns\Column;
use Cone\Root\Columns\Columns;
use Cone\Root\Columns\Relation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Search extends RenderableFilter
{
    /**
     * The searchable columns.
     */
    protected Columns $columns;

    /**
     * Create a new filter instance.
     */
    public function __construct(Columns $columns)
    {
        $this->columns = $columns;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, Builder $query, mixed $value): Builder
    {
        $attributes = $this->columns->mapWithKeys(static function (Column $column): array {
            return [
                $column->getModelAttribute() => $column instanceof Relation ? $column->getSearchableRelationAttributes() : null,
            ];
        })->all();

        if (empty($value) || empty($attributes)) {
            return $query;
        }

        return $query->where(static function (Builder $query) use ($attributes, $value): void {
            foreach ($attributes as $attribute => $columns) {
                $operator = array_key_first($attributes) === $attribute ? 'and' : 'or';

                if (is_array($columns)) {
                    $query->has($attribute, '>=', 1, $operator, static function (Builder $query) use ($columns, $value): Builder {
                        foreach ($columns as $column) {
                            $operator = $columns[0] === $column ? 'and' : 'or';

                            $query->where($query->qualifyColumn($column), 'like', "%{$value}%", $operator);
                        }

                        return $query;
                    });
                } else {
                    $query->where($query->qualifyColumn($attribute), 'like', "%{$value}%", $operator);
                }
            }
        });
    }

    /**
     * {@inheritdoc}
     */
    public function toField(): SearchField
    {
        return SearchField::make($this->getName(), $this->getRequestKey())
            ->value(fn (Request $request): ?string => $this->getValue($request))
            ->placeholder($this->getName().'...');
    }
}
