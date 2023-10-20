<?php

namespace Cone\Root\Filters;

use Cone\Root\Columns\Columns;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
        return $query;
    }

    /**
     * {@inheritdoc}
     */
    public function toField(): SearchField
    {
        return SearchField::make($this->getName(), $this->getRequestKey())
            ->value(function (Request $request, Model $model): ?string {
                return $model->getAttribute($this->getKey());
            })
            ->placeholder($this->getName().'...');
    }
}
