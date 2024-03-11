<?php

namespace Cone\Root\Resources;

use Cone\Root\Actions\SendPasswordResetNotification;
use Cone\Root\Actions\SendVerificationNotification;
use Cone\Root\Fields\Date;
use Cone\Root\Fields\Email;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Text;
use Cone\Root\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class UserResource extends Resource
{
    /**
     * The model class.
     */
    protected string $model = User::class;

    /**
     * The icon for the resource.
     */
    protected string $icon = 'users';

    /**
     * Get the model for the resource.
     */
    public function getModel(): string
    {
        return $this->model::getProxiedClass();
    }

    /**
     * Define the fields.
     */
    public function fields(Request $request): array
    {
        return [
            ID::make()->searchable(),

            Text::make(__('Name'), 'name')
                ->rules(['required', 'string', 'max:256'])
                ->searchable()
                ->sortable(),

            Email::make(__('Email'), 'email')
                ->searchable()
                ->sortable()
                ->rules(['required', 'string', 'email', 'max:256'])
                ->createRules(['unique:users'])
                ->updateRules(static function (Request $request, Model $model): array {
                    return [Rule::unique('users')->ignoreModel($model)];
                }),

            Date::make(__('Created At'), 'created_at')
                ->sortable()
                ->withTime(),

            Date::make(__('Email Verified At'), 'email_verified_at')
                ->sortable()
                ->withTime(),
        ];
    }

    /**
     * Define the actions.
     */
    public function actions(Request $request): array
    {
        return [
            new SendVerificationNotification(),
            new SendPasswordResetNotification(),
        ];
    }

    /**
     * Handle the saved form event.
     */
    public function saving(Request $request, Model $model): void
    {
        if (! $model->exists && is_null($model->getAttribute('password'))) {
            $model->setAttribute('password', Hash::make(Str::password()));
        }
    }
}
