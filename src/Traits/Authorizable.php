<?php

namespace Cone\Root\Traits;

use Closure;
use Illuminate\Http\Request;

trait Authorizable
{
    /**
     * The authorization resolver callback.
     */
    protected ?Closure $authorizationResolver = null;

    /**
     * Merge the current and the given resolvers.
     */
    public function mergeAuthorizationResolver(Closure $callback): static
    {
        if (is_null($this->authorizationResolver)) {
            return $this->authorize($callback);
        }

        $resolver = $this->authorizationResolver;

        return $this->authorize(static function (Request $request, ...$parameters) use ($callback, $resolver): bool {
            return call_user_func_array($callback, [$request, ...$parameters])
                && call_user_func_array($resolver, [$request, ...$parameters]);
        });
    }

    /**
     * Set the authorization resolver.
     */
    public function authorize(Closure $callback): static
    {
        $this->authorizationResolver = $callback;

        return $this;
    }

    /**
     * Resolve the authorization.
     *
     * @param  array  ...$parameters
     */
    public function authorized(Request $request, ...$parameters): bool
    {
        return is_null($this->authorizationResolver)
            || call_user_func_array($this->authorizationResolver, [$request, ...$parameters]);
    }
}
