<?php

namespace Cone\Root\Tests\Resources;

use Cone\Root\Fields\Email;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Text;
use Cone\Root\Resources\Resource;
use Cone\Root\Tests\Actions\SendPasswordResetNotification;
use Cone\Root\Tests\User;
use Cone\Root\Tests\Widgets\UsersCount;
use Cone\Root\Tests\Widgets\UsersTrend;
use Illuminate\Http\Request;

class UserResource extends Resource
{
    protected string $model = User::class;

    public function actions(Request $request): array
    {
        return [
            new SendPasswordResetNotification(),
        ];
    }

    public function fields(Request $request): array
    {
        return [
            ID::make(),
            Text::make('Name')->searchable(),
            Email::make('Email')->searchable(),
        ];
    }

    public function widgets(Request $request): array
    {
        return [
            new UsersCount(),
            new UsersTrend(),
        ];
    }
}
