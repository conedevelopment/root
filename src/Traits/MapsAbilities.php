<?php

namespace Cone\Root\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

trait MapsAbilities
{
    /**
     * Map the abilities.
     */
    public function mapAbilities(Request $request, Model $model): array
    {
        $policy = $this->getPolicy($model);

        return array_reduce(
            $this->getAbilities(),
            static function (array $stack, string $ability) use ($request, $policy, $model): array {
                return array_merge($stack, [
                    $ability => is_null($policy) || $request->user()->can($ability, $model),
                ]);
            },
            []
        );
    }

    /**
     * Get the policy.
     */
    public function getPolicy(string|Model $model): mixed
    {
        return Gate::getPolicyFor($model);
    }

    /**
     * Get the mappable abilities.
     */
    public function getAbilities(): array
    {
        return [];
    }
}
