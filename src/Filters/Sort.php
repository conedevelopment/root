<?php

namespace Cone\Root\Filters;

use Cone\Root\Fields\Field;
use Cone\Root\Fields\Relation;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Relation as EloquentRelation;

class Sort extends Filter
{
    /**
     * The sortable fields.
     *
     * @var \Cone\Root\Support\Collections\Fields
     */
    protected Fields $fields;

    /**
     * The Vue component.
     *
     * @var string|null
     */
    protected ?string $component = null;

    /**
     * Create a new filter instance.
     *
     * @param  \Cone\Root\Support\Collections\Fields  $fields
     * @return void
     */
    public function __construct(Fields $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Apply the filter on the query.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $value
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply(RootRequest $request, Builder $query, mixed $value): Builder
    {
        $value = array_replace(['by' => 'id', 'order' => 'desc'], (array) $value);

        $columns = $this->mapColumns();

        if (! array_key_exists($value['by'], $columns)) {
            return $query;
        }

        if ($value['by'] === 'id') {
            $value['by'] = $query->getModel()->getKeyName();
        }

        if (is_null($columns[$value['by']])) {
            return $query->orderBy($query->qualifyColumn($value['by']), $value['order']);
        }

        $relation = EloquentRelation::noConstraints(static function () use ($query, $value): EloquentRelation {
            $relation = call_user_func([$query->getModel(), $value['by']]);

            $key = $relation instanceof BelongsTo
                ? $relation->getQualifiedOwnerKeyName()
                : $relation->getQualifiedParentKeyName();

            return $relation->whereColumn($relation->getQualifiedForeignKeyName(), '=', $key);
        });

        return $query->orderBy(
            $relation->getQuery()->select($relation->qualifyColumn($columns[$value['by']])), $value['order']
        );
    }

    /**
     * The default value of the filter.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return mixed
     */
    public function default(RootRequest $request): mixed
    {
        return [
            'by' => $request->query('sort.by', Model::CREATED_AT),
            'order' => $request->query('sort.order', 'desc'),
        ];
    }

    /**
     * Map the sortable columns.
     *
     * @return array
     */
    protected function mapColumns(): array
    {
        return $this->fields->mapWithKeys(static function (Field $field): array {
            return [
                $field->getKey() => $field instanceof Relation ? $field->getSortableColumn() : null,
            ];
        })->toArray();
    }
}
