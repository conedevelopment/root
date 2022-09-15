<?php

declare(strict_types = 1);

namespace Cone\Root\Traits;

use Cone\Root\Http\Requests\RootRequest;

trait ResolvesBreadcrumbs
{
    /**
     * Resolve the breadcrumbs for the given request.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return array
     */
    public function resolveBreadcrumbs(RootRequest $request): array
    {
        return [];
    }
}
