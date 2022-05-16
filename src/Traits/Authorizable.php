<?php

namespace Cone\Root\Traits;

use Closure;
use Cone\Root\Http\Requests\RootRequest;

trait Authorizable
{
    /**
     * The authorization resolver callback.
     *
     * @var \Closure|null
     */
    protected ?Closure $authorizationResolver = null;

    /**
     * Merge the current and the given resolvers.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function mergeAuthorizationResolver(Closure $callback): static
    {
        if (is_null($this->authorizationResolver)) {
            return $this->authorize($callback);
        }

        $resolver = $this->authorizationResolver;

        return $this->authorize(static function (RootRequest $request, ...$parameters) use ($callback, $resolver): bool {
            return call_user_func_array($callback, [$request, ...$parameters])
                && call_user_func_array($resolver, [$request, ...$parameters]);
        });
    }

    /**
     * Set the authorization resolver.
     *
     * @param  \Closure  $callback
     * @return $this
     */
    public function authorize(Closure $callback): static
    {
        $this->authorizationResolver = $callback;

        return $this;
    }

    /**
     * Resolve the authorization.
     *
     * @param  \Cone\Root\Http\Requests\RootRequest  $request
     * @param  array  ...$parameters
     * @return bool
     */
    public function authorized(RootRequest $request, ...$parameters): bool
    {
        return is_null($this->authorizationResolver)
            || call_user_func_array($this->authorizationResolver, [$request, ...$parameters]);
    }
}
