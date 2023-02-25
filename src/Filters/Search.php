<?php

namespace Cone\Root\Filters;

use Cone\Root\Fields\Field;
use Cone\Root\Fields\Relation;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Support\Collections\Fields;
use Illuminate\Database\Eloquent\Builder;

class Search extends Filter
{
    /**
     * The searchable fields.
     */
    protected Fields $fields;

    /**
     * The Vue component.
     */
    protected ?string $component = 'Input';

    /**
     * Create a new filter instance.
     *
     * @return void
     */
    public function __construct(Fields $fields)
    {
        $this->fields = $fields;
    }

    /**
     * Apply the filter on the query.
     */
    public function apply(RootRequest $request, Builder $query, mixed $value): Builder
    {
        $attributes = $this->mapColumns();

        if (empty($value) || empty($attributes)) {
            return $query;
        }

        return $query->where(static function (Builder $query) use ($attributes, $value): void {
            foreach ($attributes as $attribute => $columns) {
                $boolean = array_key_first($attributes) === $attribute ? 'and' : 'or';

                if (is_array($columns)) {
                    $query->has($attribute, '>=', 1, $boolean, static function (Builder $query) use ($columns, $value): Builder {
                        foreach ($columns as $column) {
                            $boolean = $columns[0] === $column ? 'and' : 'or';

                            $query->where($query->qualifyColumn($column), 'like', "%{$value}%", $boolean);
                        }

                        return $query;
                    });
                } else {
                    $query->where($query->qualifyColumn($attribute), 'like', "%{$value}%", $boolean);
                }
            }
        });
    }

    /**
     * Map the searchable columns.
     */
    protected function mapColumns(): array
    {
        return $this->fields->mapWithKeys(static function (Field $field): array {
            return [
                $field->getKey() => $field instanceof Relation ? $field->getSearchableColumns() : null,
            ];
        })->toArray();
    }

    /**
     * {@inheritdoc}
     */
    public function toInput(RootRequest $request): array
    {
        return array_merge(parent::toInput($request), [
            'debounce' => 1000,
        ]);
    }
}
