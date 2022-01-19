<?php

namespace Cone\Root\Traits;

use Closure;
use Illuminate\Http\Request;

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
        $resolver = $this->authorizationResolver ?: static function (): bool {
            return true;
        };

        return $this->authorize(static function (Request $request) use ($callback, $resolver): bool {
            return call_user_func_array($callback, [$request]) && call_user_func_array($resolver, [$request]);
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
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    public function authorized(Request $request): bool
    {
        return is_null($this->authorizationResolver)
            || call_user_func_array($this->authorizationResolver, [$request]);
    }
}
