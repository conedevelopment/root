<?php

namespace Cone\Root\Interfaces;

use Cone\Root\Forms\Form;
use Illuminate\Http\Request;

interface HasForm
{
    /**
     * Get the form representation of the resource.
     */
    public function toForm(Request $request): Form;
}