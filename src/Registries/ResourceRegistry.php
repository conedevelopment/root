<?php

namespace Cone\Root\Registries;

use Cone\Root\Exceptions\ResourceResolutionException;
use Cone\Root\Interfaces\Registries\ResourceRegistry as Contract;
use Cone\Root\Resources\Resource;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ResourceRegistry extends Registry implements Contract
{
    /**
     * Resolve the resource by its key.
     *
     * @param  string  $key
     * @return \Cone\Root\Resources\Resource
     *
     * @throws \Cone\Root\Exceptions\ResourceResolutionException
     */
    public function resolve(string $key): Resource
    {
        if (! $this->has($key)) {
            throw new ResourceResolutionException("Unable to resolve resource with key [{$key}].");
        }

        return $this->get($key);
    }

    /**
     * Resolve the resource from the given request.
     *
     * @param  \Illuminate\Http\Request  $requests
     * @return \Cone\Root\Resources\Resource
     *
     * @throws \Symfony\Component\HttpKernel\Exception\NotFoundHttpException
     */
    public function resolveFromRequest(Request $request): Resource
    {
        try {
            return $this->resolve($request->route('resource'));
        } catch (ResourceResolutionException $exception) {
            throw new NotFoundHttpException($exception->getMessage());
        }
    }
}
