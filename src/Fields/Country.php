<?php

namespace Cone\Root\Fields;

use Cone\Root\Support\Countries;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class Country extends Field
{
    /**
     * The Vue compoent.
     *
     * @var string
     */
    protected string $component = 'Select';

    /**
     * Get the input representation of the field.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return array
     */
    public function toInput(Request $request, Model $model): array
    {
        return array_merge(parent::toInput($request, $model), [
            'options' => Countries::all(),
        ]);
    }
}
