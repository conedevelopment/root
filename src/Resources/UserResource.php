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
     * {@inheritdoc}
     */
    public function fields(Request $request): array
    {
        return [
            ID::make(),

            Text::make(__('Name'), 'name')
                ->rules(['required', 'string', 'max:256']),

            Text::make(__('Email'), 'email')
                ->type('email')
                ->rules(['required', 'string', 'email', 'max:256'])
                ->createRules(['unique:users'])
                ->updateRules(static function (Request $request, Model $model): array {
                    return [Rule::unique('users')->ignoreModel($model)];
                }),
        ];
    }
}
