<?php

namespace Cone\Root\Traits;

use Cone\Root\Http\Requests\RootRequest;
use Illuminate\Support\Str;

trait ResolvesBreadcrumbs
{
    /**
     * Resolve the title for the given request.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @return string
     */
    public function resolveTitle(RootRequest $request): string
    {
        return Str::headline(class_basename(static::class));
    }

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
