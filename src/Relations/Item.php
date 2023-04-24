<?php

namespace Cone\Root\Relations;

use Cone\Root\Resources\Item as BaseItem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class Item extends BaseItem
{
    /**
     * The relation name.
     */
    protected string $relation;

    /**
     * Create a new item instance.
     */
    public function __construct(Model $model, string $relation)
    {
        parent::__construct($model);

        $this->relation = $relation;
    }

    /**
     * Get the policy for the model.
     */
    public function getPolicy(): mixed
    {
        return Gate::getPolicyFor($this->model->getRelation('parent'));
    }

    /**
     * Resolve the abilities.
     */
    public function getAbilities(): array
    {
        $policy = $this->getPolicy();

        $parent = $this->model->getRelation('parent');

        $relation = Str::of($this->relation)->singular()->ucfirst()->value();

        return [
            'view' => is_null($policy) || Gate::allows('view'.$relation, [$parent, $this->model]),
            'update' => is_null($policy) || Gate::allows('update'.$relation, [$parent, $this->model]),
            'delete' => is_null($policy) || Gate::allows('delete'.$relation, [$parent, $this->model]),
        ];
    }
}
