<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Extracts\Extract;
use Cone\Root\Support\Collections\Extracts;
use Illuminate\Http\Request;

trait ResolvesExtracts
{
    /**
     * The extracts resolver callback.
     */
    protected ?Closure $extractsResolver = null;

    /**
     * The resolved extracts.
     */
    protected ?Extracts $extracts = null;

    /**
     * Define the extracts for the resource.
     */
    public function extracts(Request $request): array
    {
        return [];
    }

    /**
     * Set the extracts resolver.
     */
    public function withExtracts(array|Closure $extracts): static
    {
        $this->extractsResolver = is_array($extracts) ? fn (): array => $extracts : $extracts;

        return $this;
    }

    /**
     * Resolve the extracts.
     */
    public function resolveExtracts(Request $request): Extracts
    {
        if (is_null($this->extracts)) {
            $this->extracts = Extracts::make()->register($this->extracts($request));

            if (! is_null($this->extractsResolver)) {
                $this->extracts->register(call_user_func_array($this->extractsResolver, [$request]));
            }

            $this->extracts->each(function (Extract $extract) use ($request): void {
                $this->resolveExtract($request, $extract);
            });
        }

        return $this->extracts;
    }

    /**
     * Handle the resolving event on the extract instance.
     */
    protected function resolveExtract(Request $request, Extract $extract): void
    {
        //
    }
}
