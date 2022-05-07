<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Support\Collections\Extracts;
use Illuminate\Http\Request;

trait ResolvesExtracts
{
    /**
     * The extracts resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $extractsResolver = null;

    /**
     * The resolved extracts.
     *
     * @var \Cone\Root\Support\Collections\Extracts|null
     */
    protected ?Extracts $resolvedExtracts = null;

    /**
     * Define the extracts for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function extracts(Request $request): array
    {
        return [];
    }

    /**
     * Set the extracts resolver.
     *
     * @param  array|\Closure  $extracts
     * @return $this
     */
    public function withExtracts(array|Closure $extracts): static
    {
        if (is_array($extracts)) {
            $extracts = static function (Request $request, Extracts $collection) use ($extracts): Extracts {
                return $collection->merge($extracts);
            };
        }

        $this->extractsResolver = $extracts;

        return $this;
    }

    /**
     * Resolve the extracts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Cone\Root\Support\Collections\Extracts
     */
    public function resolveExtracts(Request $request): Extracts
    {
        if (is_null($this->resolvedExtracts)) {
            $extracts = Extracts::make($this->extracts($request));

            if (! is_null($this->extractsResolver)) {
                $extracts = call_user_func_array($this->extractsResolver, [$request, $extracts]);
            }

            $this->resolvedExtracts = $extracts->each->mergeAuthorizationResolver(function (...$parameters): bool {
                return $this->authorized(...$parameters);
            });
        }

        return $this->resolvedExtracts;
    }
}
