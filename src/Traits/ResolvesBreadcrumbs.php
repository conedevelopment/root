<?php

namespace Cone\Root\Traits;

use Cone\Root\Http\Requests\RootRequest;

trait ResolvesBreadcrumbs
{
    /**
     * Resolve the breadcrumbs for the given request.
     */
    public function resolveBreadcrumbs(RootRequest $request): array
    {
        return [];
    }
}
