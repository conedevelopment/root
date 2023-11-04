<?php

namespace Cone\Root\Filters;

use Cone\Root\Fields\Field;
use Cone\Root\Fields\Fields;
use Cone\Root\Fields\Relation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class Search extends RenderableFilter
{
    /**
     * The searchable fields.
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
        $attributes = $this->fields->mapWithKeys(static function (Field $field): array {
            return [
                $field->getModelAttribute() => $field instanceof Relation ? $field->getSearchableColumns() : null,
            ];
        })->all();

        if (empty($value) || empty($attributes)) {
            return $query;
        }

        return $query->where(static function (Builder $query) use ($attributes, $value): void {
            foreach ($attributes as $attribute => $fields) {
                $operator = array_key_first($attributes) === $attribute ? 'and' : 'or';

                if (is_array($fields)) {
                    $query->has($attribute, '>=', 1, $operator, static function (Builder $query) use ($fields, $value): Builder {
                        foreach ($fields as $field) {
                            $operator = $fields[0] === $field ? 'and' : 'or';

                            $query->where($query->qualifyColumn($field), 'like', "%{$value}%", $operator);
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
