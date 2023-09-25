<?php

namespace Cone\Root\Interfaces;

use Cone\Root\Form\Form;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

interface AsForm
{
    /**
     * Convert the object to a form using the request and the model.
     */
    public function toForm(Request $request, Model $model): Form;
}
