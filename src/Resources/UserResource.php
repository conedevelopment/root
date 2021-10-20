<?php

namespace Cone\Root\Resources;

use Cone\Root\Fields\ID;
use Cone\Root\Fields\Text;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

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

            Text::make(__('Name'), 'name')
                ->rules(['required', 'string', 'max:256']),

            Text::make(__('Email'), 'email')
                ->type('email')
                ->rules(static function (Request $request, Model $model): array {
                    return [
                        '*' => ['required', 'string', 'email', 'max:256'],
                        static::CREATE => ['unique:users'],
                        static::UPDATE => [Rule::unique('users')->ignoreModel($model)],
                    ];
                }),
        ];
    }
}
