<?php

namespace Cone\Root\Fields;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class File extends Field
{
    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'File';

    /**
     * Hydrate the model.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @param  mixed  $value
     * @return void
     */
    public function hydrate(Request $request, Model $model, mixed $value): void
    {
        $model->saving(function (Model $model) use ($value): void {
            $model->setAttribute($this->name, $value);
        });
    }
}
