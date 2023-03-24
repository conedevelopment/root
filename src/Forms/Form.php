<?php

namespace Cone\Root\Forms;

use Cone\Root\Support\Collections\Fields;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Contracts\Support\Responsable;
use Illuminate\Database\Eloquent\Model;

class Form implements Arrayable, Responsable
{
    /**
     * The model instance.
     */
    protected Model $model;

    /**
     * The fields collection.
     */
    public readonly Fields $fields;

    /**
     * Create a new form instance.
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
        $this->fields = new Fields();
    }

    /**
     * Get the instance as an array.
     */
    public function toArray(): array
    {
        return [];
    }

    /**
     * Create an HTTP response that represents the object.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function toResponse($request)
    {
        //
    }
}
