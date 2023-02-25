<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Extracts\Extract;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Support\Collections\Extracts;

trait ResolvesExtracts
{
    /**
     * The extracts resolver callback.
     */
    protected ?Closure $extractsResolver = null;

    /**
     * The resolved extracts.
     */
    protected ?Extracts $resolvedExtracts = null;

    /**
     * Define the extracts for the resource.
     */
    public function extracts(RootRequest $request): array
    {
        return [];
    }

    /**
     * Set the extracts resolver.
     */
    public function withExtracts(array|Closure $extracts): static
    {
        if (is_array($extracts)) {
            $extracts = static function (RootRequest $request, Extracts $collection) use ($extracts): Extracts {
                return $collection->merge($extracts);
            };
        }

        $this->extractsResolver = $extracts;

        return $this;
    }

    /**
     * Resolve the extracts.
     */
    public function resolveExtracts(RootRequest $request): Extracts
    {
        if (is_null($this->resolvedExtracts)) {
            $extracts = Extracts::make($this->extracts($request));

            if (! is_null($this->extractsResolver)) {
                $extracts = call_user_func_array($this->extractsResolver, [$request, $extracts]);
            }

            $this->resolvedExtracts = $extracts->each(function (Extract $extract) use ($request): void {
                $this->resolveExtract($request, $extract);
            });
        }

        return $this->resolvedExtracts;
    }

    /**
     * Handle the resolving event on the extract instance.
     */
    protected function resolveExtract(RootRequest $request, Extract $extract): void
    {
        //
    }
}
