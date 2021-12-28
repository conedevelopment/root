<?php

namespace Cone\Root\Fields;

use Cone\Root\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class File extends Media
{
    /**
     * Indicates if the component is async.
     *
     * @var bool
     */
    protected bool $async = false;

    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'File';

    /**
     * Get the value for hydrating the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return mixed
     */
    public function getValueForHydrate(Request $request, Model $model): mixed
    {
        return $request->file($this->name);
    }

    /**
     * Regsiter the routes for the async component.
     *
     * @param  \Cone\Root\Resources\Resource  $resource
     * @param  string  $uri
     * @return void
     */
    protected function routes(Resource $resource, string $uri): void
    {
        //
    }
}
