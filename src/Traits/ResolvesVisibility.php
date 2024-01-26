<?php

namespace Cone\Root\Traits;

use Closure;
use Illuminate\Support\Arr;

trait ResolvesVisibility
{
    /**
     * The visibility resolver callbacks.
     */
    protected array $visibilityResolvers = [];

    /**
     * Determine if the object is visible for the given request.
     */
    public function visible(string|array $context): bool
    {
        foreach ($this->visibilityResolvers as $callback) {
            if (! call_user_func_array($callback, [$context])) {
                return false;
            }
        }

        return true;
    }

    /**
     * Set a custom visibility resolver.
     */
    public function visibleOn(string|array|Closure $context): static
    {
        $this->visibilityResolvers[] = $context instanceof Closure
            ? $context
            : static function (string|array $currentContext) use ($context) {
                return ! empty(array_intersect(Arr::wrap($currentContext), Arr::wrap($context)));
            };

        return $this;
    }

    /**
     * Set a custom hidden resolver.
     */
    public function hiddenOn(string|array|Closure $context): static
    {
        return $this->visibleOn(static function (array|string $currentContext) use ($context): bool {
            return $context instanceof Closure
                ? ! call_user_func_array($context, [$currentContext])
                : empty(array_intersect(Arr::wrap($currentContext), Arr::wrap($context)));
        });
    }
}
