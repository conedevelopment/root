<?php

namespace Cone\Root\Resources;

use Cone\Root\Fields\ID;
use Cone\Root\Fields\Text;
use Illuminate\Http\Request;

class UserResource extends Resource
{
    /**
     * Define the fields for the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(),
            Text::make(__('Name'), 'name'),
            Text::make(__('Email'), 'email')->type('email'),
        ];
    }
}
