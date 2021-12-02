<?php

namespace Cone\Root\Traits;

use Closure;
use Illuminate\Http\Request;

trait Authorizable
{
    /**
     * The authorization resolver.
     *
     * @var \Closure|null
     */
    protected ?Closure $authorizationResolver = null;

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
    public function resolveAuthroziation(Request $request): bool
    {
        return is_null($this->authorizationResolver)
            || call_user_func_array($this->authorizationResolver, [$request]);
    }
}
