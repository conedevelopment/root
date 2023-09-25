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
     * Set the authorization resolver.
     */
    public function authorize(Closure $callback): static
    {
        $this->authorizationResolver = $callback;

        return $this;
    }

    /**
     * Resolve the authorization.
     */
    public function authorized(Request $request, ...$parameters): bool
    {
        return is_null($this->authorizationResolver)
            || call_user_func_array($this->authorizationResolver, [$request, ...$parameters]);
    }
}
