<?php

declare(strict_types = 1);

namespace Cone\Root\Traits;

use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Gate;

trait MapsAbilities
{
    /**
     * Map the abilities.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function mapAbilities(RootRequest $request, Model $model): array
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
     *
     * @param  \Illuminate\Database\Eloquent\Model|string  $model
     * @return mixed
     */
    public function getPolicy(string|Model $model): mixed
    {
        return Gate::getPolicyFor($model);
    }

    /**
     * Get the mappable abilities.
     *
     * @return array
     */
    public function getAbilities(): array
    {
        return [];
    }
}
