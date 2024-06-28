<?php

namespace Cone\Root\Tests\Resources;

use Cone\Root\Fields\BelongsToMany;
use Cone\Root\Fields\Email;
use Cone\Root\Fields\HasMany;
use Cone\Root\Fields\ID;
use Cone\Root\Fields\Media;
use Cone\Root\Fields\MorphTo;
use Cone\Root\Fields\Repeater;
use Cone\Root\Fields\Select;
use Cone\Root\Fields\Text;
use Cone\Root\Resources\Resource;
use Cone\Root\Tests\Actions\SendPasswordResetNotification;
use Cone\Root\Tests\Team;
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
            Text::make('Password')->visibleOn(['create', 'update']),
            Media::make('Media'),
            HasMany::make('Uploads')
                ->display('file_name')
                ->asSubResource()
                ->withFields(function () {
                    return [
                        Text::make('Name'),
                        Text::make('File Name'),
                        Text::make('Size'),
                        Text::make('Disk'),
                        Text::make('Mime Type'),
                    ];
                }),
            BelongsToMany::make('Teams')
                ->display('name')
                ->asSubResource()
                ->withPivotFields(function () {
                    return [
                        Select::make('Role')
                            ->options([
                                'admin' => 'Admin',
                                'member' => 'Member',
                            ]),
                    ];
                }),
            MorphTo::make('Employer')
                ->display('name')
                ->nullable()
                ->types([
                    User::class,
                    Team::class,
                ]),
            Repeater::make('Settings')
                ->withFields(function () {
                    return [
                        Text::make('Key'),
                        Text::make('Value'),
                    ];
                }),
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
