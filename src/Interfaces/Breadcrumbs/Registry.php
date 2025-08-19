<?php

declare(strict_types=1);

namespace Cone\Root\Interfaces\Breadcrumbs;

use Closure;
use Illuminate\Http\Request;

interface Registry
{
    /**
     * Register a set of patterns.
     */
    public function patterns(array $patterns): void;

    /**
     * Register a pattern.
     */
    public function pattern(string $pattern, Closure|string $label): void;

    /**
     * Resolve the breadcrumbs using the given request.
     */
    public function resolve(Request $request): array;
}
