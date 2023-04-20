<?php

namespace Cone\Root\Relations;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PivotItem extends Item
{
    /**
     * Resolve the abilities.
     */
    protected function resolveAbilities(): array
    {
        $policy = $this->getPolicy();

        $parent = $this->model->getRelation('parent');

        $related = $this->model->getRelation('related');

        $relation = Str::of($this->model->rootRelation)->singular()->ucfirst()->value();

        return [
            'view' => is_null($policy) || Gate::allows('view'.$relation.'Pivot', [$parent, $related]),
            'update' => is_null($policy) || Gate::allows('update'.$relation.'Pivot', [$parent, $related]),
            'delete' => is_null($policy) || Gate::allows('delete'.$relation.'Pivot', [$parent, $related]),
        ];
    }
}
