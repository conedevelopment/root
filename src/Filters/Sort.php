<?php

namespace Cone\Root\Filters;

use Cone\Root\Fields\Field;
use Cone\Root\Fields\Fields;
use Cone\Root\Fields\Relation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;
use Illuminate\Http\Request;

class Sort extends Filter
{
    /**
     * The sortable fields.
     */
    protected Fields $fields;

    /**
     * Create a new filter instance.
     */
    public function __construct(Fields $fields)
    {
        $this->fields = $fields;
    }

    /**
     * {@inheritdoc}
     */
    public function apply(Request $request, Builder $query, mixed $value): Builder
    {
        $value = array_replace(['by' => 'id', 'order' => 'desc'], (array) $value);

        $attributes = $this->fields->mapWithKeys(static function (Field $field): array {
            return [
                $field->getModelAttribute() => $field instanceof Relation ? $field->getSortableColumn() : null,
            ];
        })->all();

        if (! array_key_exists($value['by'], $attributes)) {
            return $query;
        }

        if ($value['by'] === 'id') {
            $value['by'] = $query->getModel()->getKeyName();
        }

        if (is_null($attributes[$value['by']])) {
            return $query->orderBy($query->qualifyColumn($value['by']), $value['order']);
        }

        $relation = EloquentRelation::noConstraints(static function () use ($query, $value): EloquentRelation {
            $relation = call_user_func([$query->getModel(), $value['by']]);

            $key = $relation instanceof BelongsTo
                ? $relation->getQualifiedOwnerKeyName()
                : $relation->getQualifiedParentKeyName();

            return $relation->whereField($relation->getQualifiedForeignKeyName(), '=', $key);
        });

        return $query->orderBy(
            $relation->getQuery()->select($relation->qualifyColumn($attributes[$value['by']])), $value['order']
        );
    }
}
