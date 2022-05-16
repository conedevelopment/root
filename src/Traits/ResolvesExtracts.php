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
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function extracts(RootRequest $request): array
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
            $extracts = static function (RootRequest $request, Extracts $collection) use ($extracts): Extracts {
                return $collection->merge($extracts);
            };
        }

        $this->extractsResolver = $extracts;

        return $this;
    }

    /**
     * Resolve the extracts.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return \Cone\Root\Support\Collections\Extracts
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
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  \Cone\Root\Extracts\Extract  $extract
     * @return void
     */
    protected function resolveExtract(RootRequest $request, Extract $extract): void
    {
        //
    }
}
