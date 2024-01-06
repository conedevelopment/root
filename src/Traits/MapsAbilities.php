<?php

namespace Cone\Root\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

trait MapsAbilities
{
        /**
     * Map the resource abilities.
     */
    public function mapAbilities(): array
    {
        return [
            'viewAny' => function (Request $request): bool {
                return is_null($this->getPolicy()) || Gate::allows('viewAny', $this->getModel());
            },
            'create' => function (Request $request): bool {
                return is_null($this->getPolicy()) || Gate::allows('create', $this->getModel());
            },
            'view' => function (Request $request, Model $model): bool {
                return is_null($this->getPolicy()) || Gate::allows('view', $model);
            },
            'update' => function (Request $request, Model $model): bool {
                return is_null($this->getPolicy()) || Gate::allows('update', $model);
            },
            'delete' => function (Request $request, Model $model): bool {
                return is_null($this->getPolicy()) || Gate::allows('delete', $model);
            },
            'forceDelete' => function (Request $request, Model $model): bool {
                return is_null($this->getPolicy()) || Gate::allows('delete', $model);
            },
            'restore' => function (Request $request, Model $model): bool {
                return is_null($this->getPolicy()) || Gate::allows('delete', $model);
            },
        ];
    }
}
