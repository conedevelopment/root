<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Http\Requests\RootRequest;
use Cone\Root\Support\Breadcrumbs;

trait ResolvesBreadcrumbs
{
    /**
     * The breadcrumbs resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $breadcrumbsResolver = null;

    /**
     * Set the breadcrumbs resolver.
     *
     * @param  \Closure  $callback
     * @return static
     */
    public function withBreadcrumbs(Closure $callback): static
    {
        $this->breadcrumbsResolver = $callback;

        return $this;
    }

    /**
     * Resolve the breadcrumbs.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  array  ...$parameters
     * @return \Cone\Root\Support\Breadcrumbs
     */
    public function resolveBreadcrumbs(RootRequest $request, ...$parameters): Breadcrumbs
    {
        if (is_null($this->breadcrumbsResolver)) {
            return new Breadcrumbs();
        }

        return call_user_func_array($this->breadcrumbsResolver, [$request, ...$parameters]);
    }
}
